<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Separator from '@/components/ui/separator/Separator.vue';
import { Fingerprint, Mail } from 'lucide-vue-next';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';
import { ofetch } from 'ofetch';
import { WebAuthn } from '@/lib/webauthn';
import { useCSRF } from '@/Composables/useCSRF';

const {
    mfa_verification_token,
} = defineProps<{
    mfa_verification_token: string;
}>();

const {
    csrf_token,
} = useCSRF();

const webauthnOpen = ref(false);
const keyNameInput = ref<string>("");

const setupWebAuthn = async () => {

    webauthnOpen.value = false;

    const key = await ofetch(route('profile.webauthn.store.options'), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf_token.value,
        },
    });
    const webauthn = new WebAuthn();
    const credential = await webauthn.register(key.publicKey, keyNameInput.value);
    await ofetch(route('profile.webauthn.store'), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf_token.value,
            'Content-Type': 'application/json',
            'X-MFA-VERIFICATION-TOKEN': mfa_verification_token,
        },
        body: credential,
    });

    router.visit(route('dashboard'));
}

const setupMail = () => {
    router.visit(route('dashboard'));
}
</script>

<template>
    <GuestLayout heading="Multi Factor Authentication"
        subheading="Do you want to enable Multi Factor Authentication for an additional layer of security on your account?">

        <Head title="Activate Account" />

        <p class="mb-6 text-gray-600 dark:text-gray-400">
            Enabling Multi Factor Authentication (MFA) helps protect your account by requiring an additional
            verification step during login. You can choose to set up MFA now or skip this step and do it later in your
            profile settings.
        </p>

        <div class="flex w-full">
            <div class="flex-1 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md p-4"
                @click="webauthnOpen = true">
                <Fingerprint :size="32" class="mb-4 text-gray-500 dark:text-gray-400" />
                <p class="text-md font-small text-gray-900 dark:text-gray-100 mb-2 px-4 text-center">Trusted device</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 px-4 text-center">Use your fingerprint or security
                    key for faster and more secure sign-ins.</p>
            </div>
            <div class="flex flex-col justify-center">
                <Separator orientation="vertical" class="h-16 mx-6" />
            </div>
            <div class="flex-1 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md p-4"
                @click="setupMail">
                <Mail :size="32" class="mb-4 text-gray-500 dark:text-gray-400" />
                <p class="text-md font-small text-gray-900 dark:text-gray-100 mb-2 px-4 text-center">Via email</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 px-4 text-center">Get a one-time code each time you
                    sign in. (not recommended)</p>
            </div>


            <Modal :show="webauthnOpen" @close="webauthnOpen = false">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Register New Security Key
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Please follow the instructions of your security key to
                        complete the registration.
                    </p>

                    <div class="mt-6">
                        <InputLabel for="keyName" value="Key Name" class="sr-only" />

                        <TextInput id="keyName" v-model="keyNameInput" type="text" class="mt-1 block w-3/4"
                            placeholder="Key Name" required @keyup.enter="setupWebAuthn" autofocus />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="webauthnOpen = false">
                            Cancel
                        </SecondaryButton>

                        <PrimaryButton class="ms-3" @click="setupWebAuthn">
                            Register Key
                        </PrimaryButton>
                    </div>
                </div>
            </Modal>
        </div>
    </GuestLayout>
</template>
