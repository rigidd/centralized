<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'picture' => $this->picture,
            'redirect_urls' => collect(explode(',', $this->redirect))->map(function ($url) {
                // Return matching alias or a default object
                $alias = $this->redirectAliases->where('url', $url)->first();
                return [
                    'url' => $url,
                    'alias' => $alias ? $alias->alias : null,
                    'icon' => $alias ? $alias->icon : null,
                ];
            })->values()->all(),
            'secret' => $this->when($request->method() === 'POST', $this->secret),
            'display' => $this->display,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
