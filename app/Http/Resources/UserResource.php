<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_password_expired' => $this->password_expired_at ? $this->password_expired_at->isPast() : false,
            'active_client_sessions' => ClientResource::collection($this->whenLoaded('active_client_sessions')),
            'clients' => ClientResource::collection($this->whenLoaded('clients')),
            'groups' => GroupResource::collection($this->whenLoaded('groups')),
            'has_webauthn_enabled' => $this->hasWebauthnEnabled(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
