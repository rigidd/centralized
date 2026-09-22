# Centralized

Centralized is a lightweight, self-hosted Identity and Single Sign-On (SSO) provider built with Laravel 12, Inertia.js, Vue 3, and FrankenPHP.

It provides centralized OAuth2 and OpenID Connect (OIDC) authentication across multiple client applications, complete with hardware-backed MFA (WebAuthn / Passkeys), user sessions audit logs, and granular access management.

---

## Features

- **OAuth 2.0 & OpenID Connect**: Turn the platform into an identity provider for internal tools, third-party apps, or custom clients via Laravel Passport and OIDC extensions.
- **Client & Redirect Management**: Configure OAuth clients, allowed redirect URIs, and custom redirect aliases per application.
- **Hardware MFA & Passkeys**: Integrated WebAuthn support (FIDO2 / Security keys / Biometrics) alongside standard authentication.
- **Session & Event Auditing**: Track active user sessions, login history, and security events with remote session revocation.
- **Modern Inertia Stack**: Vue 3 with Tailwind CSS front-end served directly from Laravel via Inertia.js.
- **Cloud Native Deployment**: Pre-configured for FrankenPHP / Octane with Dockerfiles, Helm charts, and CI pipelines for Kubernetes and ArgoCD.

---

## Tech Stack

- **Backend**: PHP 8.4+, Laravel 12, Laravel Octane (FrankenPHP)
- **Frontend**: Vue 3, Inertia.js, Tailwind CSS, TypeScript
- **Auth & Protocols**: Laravel Passport, OpenID Connect (`laravel-openid-connect`), WebAuthn (`laravel-webauthn`)
- **Database / Cache**: PostgreSQL, Redis
- **DevOps**: Docker, Helm, Drone CI / ArgoCD

---

## Getting Started

### Local Development

1. **Clone repository and install dependencies**:
   ```bash
   git clone https://github.com/rigidd/centralized.git
   cd centralized
   composer install
   npm install
   ```

2. **Environment configuration**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database setup**:
   Configure database credentials in `.env`, then run migrations and OAuth key generation:
   ```bash
   php artisan migrate
   php artisan passport:keys
   ```

4. **Run dev servers**:
   ```bash
   npm run dev
   php artisan serve
   ```

### Running with Docker

A Docker compose setup is included for quick local spin-up:

```bash
docker compose up -d
```

---

## Kubernetes & Deployment

Helm chart definitions are located in `templates/` and configured via [values.yaml](values.yaml).

- Chart configuration supports mounting external Kubernetes secrets (`existingSecret.enabled=true`) for production secrets (`APP_KEY`, database credentials, OAuth keys).
- Health probes are configured against `/up`.

---

## License

This project is licensed under the [PolyForm Noncommercial License 1.0.0](https://polyformproject.org/licenses/noncommercial/1.0.0).

- **Non-Commercial Use**: You are free to inspect, evaluate, run, and modify the software for personal, educational, or internal non-commercial purposes.
- **Commercial Use**: Any use involving commercial advantage or monetary compensation requires explicit prior written permission.
- **Attribution**: Copyright (c) 2024-present Nicolas Jacquemin. See [LICENSE](LICENSE) for details.
