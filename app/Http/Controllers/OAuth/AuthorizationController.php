<?php

namespace App\Http\Controllers\OAuth;

use App\Models\User as ModelsUser;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Passport\Bridge\User;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Contracts\AuthorizationViewResponse;
use Laravel\Passport\Http\Controllers\ConvertsPsrResponses;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequestInterface;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationController
{
    use ConvertsPsrResponses, HandlesOAuthErrors;

    public function __construct(
        protected AuthorizationServer $server,
        protected ClientRepository $clients,
        ) {}

    public function authorize(
        ServerRequestInterface $psrRequest,
        Request $request,
    )
    {
        $authRequest = $this->withErrorHandling(
            fn (): AuthorizationRequestInterface => $this->server->validateAuthorizationRequest($psrRequest),
            ($psrRequest->getQueryParams()['response_type'] ?? null) === 'token'
        );

        if (Auth::guest()) {
            return $request->get('prompt') === 'none'
                    ? $this->denyRequest($authRequest)
                    : $this->promptForLogin($request);
        }

        if ($request->get('prompt') === 'login') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->promptForLogin($request);
        }

        /** @var ModelsUser $user */
        $user = Auth::user();
        $authRequest->setUser(new User($user->getAuthIdentifier()));

        $scopes = $this->parseScopes($authRequest);
        $client = $this->clients->find($authRequest->getClient()->getIdentifier());

        if ($request->get('prompt') === 'none') {
            return $this->denyRequest($authRequest, $user);
        }

        if ($user->clients()->where('oauth_clients.id', $client->id)->doesntExist() && $user->role !== 'admin') {
            return Inertia::render('OAuth/Unauthorized');
        }

        $request->session()->put('authToken', $authToken = Str::random());
        $request->session()->put('authRequest', $authRequest);

        return Inertia::render('OAuth/Authorize', [
            'client' => $client,
            'user' => $user,
            'scopes' => $scopes,
            'request' => $request,
            'state' => $request->state,
            'authToken' => $authToken,
        ]);
    }

    protected function parseScopes(AuthorizationRequestInterface $authRequest): array
    {
        return Passport::scopesFor(
            collect($authRequest->getScopes())->map(
                fn (ScopeEntityInterface $scope): string => $scope->getIdentifier()
            )->unique()->all()
        );
    }

    protected function approveRequest(AuthorizationRequestInterface $authRequest, ResponseInterface $psrResponse): Response
    {
        $authRequest->setAuthorizationApproved(true);

        return $this->withErrorHandling(fn () => $this->convertResponse(
            $this->server->completeAuthorizationRequest($authRequest, $psrResponse)
        ), $authRequest->getGrantTypeId() === 'implicit');
    }

    protected function denyRequest($authRequest, $user = null)
    {
        if (is_null($user)) {
            $uri = $authRequest->getRedirectUri()
                ?? (is_array($authRequest->getClient()->getRedirectUri())
                    ? $authRequest->getClient()->getRedirectUri()[0]
                    : $authRequest->getClient()->getRedirectUri());

            $separator = $authRequest->getGrantTypeId() === 'implicit' ? '#' : '?';

            $uri = $uri.(str_contains($uri, $separator) ? '&' : $separator).'state='.$authRequest->getState();

            return $this->withErrorHandling(function () use ($uri) {
                throw OAuthServerException::accessDenied('Unauthenticated', $uri);
            });
        }

        $authRequest->setUser(new User($user->getAuthIdentifier()));

        $authRequest->setAuthorizationApproved(false);

        return $this->withErrorHandling(function () use ($authRequest) {
            return $this->convertResponse(
                $this->server->completeAuthorizationRequest($authRequest, new Psr7Response)
            );
        });
    }

    protected function promptForLogin(Request $request)
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'fromOauth' => true,
            'requestParams' => $request->all(),
        ]);
    }
}
