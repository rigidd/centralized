export class WebAuthn {
    private bufferDecode(value: string) {
        return Uint8Array.from(atob(value), c => c.charCodeAt(0));
    }

    private base64Decode(value: string) {
        let input = value.replace(/-/g, '+').replace(/_/g, '/');

        const pad = input.length % 4;
        if (pad) {
            if (pad === 1) {
                throw new Error('InvalidLengthError: Input base64url string is the wrong length to determine padding');
            }
            input += new Array(5 - pad).join('=');
        }

        return input;
    }

    private credentialDecode(credentials: any[]) {
        let self = this;
        return credentials.map(function (data) {
            return {
                id: self.bufferDecode(self.base64Decode(data.id)),
                type: data.type,
                transports: data.transports,
            };
        });
    }

    private bufferEncode(buffer: ArrayBuffer) {
        return btoa(
            String.fromCharCode(...new Uint8Array(buffer))
        )
            .replace(/\+/g, '-')
            .replace(/\//g, '_')
            .replace(/=+$/, '');
    }

    private credentialToObject(cred: PublicKeyCredential) {
        const response = cred.response as AuthenticatorAttestationResponse;

        return {
            id: cred.id,
            rawId: this.bufferEncode(cred.rawId),
            type: cred.type,
            response: {
                clientDataJSON: this.bufferEncode(response.clientDataJSON),
                attestationObject: this.bufferEncode(response.attestationObject),
            },
        };
    }

    public async register(publicKey: any, name: string) {
        let publicKeyCredential = Object.assign({}, publicKey);
        publicKeyCredential.challenge = this.bufferDecode(this.base64Decode(publicKey.challenge));
        publicKeyCredential.user.id = this.bufferDecode(publicKey.user.id);
        if (publicKey.excludeCredentials) {
            publicKeyCredential.excludeCredentials = this.credentialDecode(publicKey.excludeCredentials);
        }

        const creds = await navigator.credentials.create({
            publicKey: publicKeyCredential
        });

        if (!creds) {
            return null;
        }

        return {
            name,
            ...this.credentialToObject(creds as PublicKeyCredential),
        };
    }

    public async authenticate(publicKey: any) {
        let publicKeyCredential = Object.assign({}, publicKey);
        publicKeyCredential.challenge = this.bufferDecode(this.base64Decode(publicKey.challenge));
        if (publicKey.allowCredentials) {
            publicKeyCredential.allowCredentials = this.credentialDecode(publicKey.allowCredentials);
        }

        return await navigator.credentials.get({
            publicKey: publicKeyCredential
        });
    }
}