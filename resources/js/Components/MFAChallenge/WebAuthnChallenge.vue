<script setup lang="ts">
import { useUser } from '@/Composables/useUser';
import { Smartphone } from 'lucide-vue-next';
import { ofetch } from 'ofetch';
import { useCSRF } from '@/Composables/useCSRF';
import { WebAuthn } from '@/lib/webauthn';
import { onMounted } from 'vue';

const {
    mfa_methods,
} = useUser();

const {
    csrf_token
} = useCSRF();

const emits = defineEmits<{
    (e: 'verified', token: string): void;
}>();

const startChallenge = async () => {
    const webauthnKey = await ofetch(route('mfa_challenge.webauthn.get_public_key'), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf_token.value,
        },
    });

    const webauthn = new WebAuthn();
    const credential = await webauthn.authenticate(webauthnKey.publicKey);

    const response = await ofetch(route('mfa_challenge.webauthn.verify'), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf_token.value,
            'Content-Type': 'application/json',
        },
        body: credential,
    });

    emits('verified', response.mfa_verification_token);
};

onMounted(() => {
    if (mfa_methods.value.includes('webauthn')) {
        startChallenge();
    }
});
</script>

<template>
    <div>
        <div v-if="mfa_methods.includes('webauthn')" @click="startChallenge"
            class="flex-1 cursor-pointer text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-md p-3">
            <Smartphone class="inline-block mr-2 mb-1" :size="20" />
            Use Security Key
        </div>
    </div>
</template>
