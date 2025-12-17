<script setup lang="ts">
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import MFAChallenge from '@/Components/MFAChallenge/MFAChallenge.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Button from '@/Components/ui/button/Button.vue';
import { useCSRF } from '@/Composables/useCSRF';
import { WebAuthn } from '@/lib/webauthn';
import { WebauthnKey } from '@/models/WebauthnKey';
import { router, usePage } from '@inertiajs/vue3';
import { CalendarDays, ShieldCheck, Smartphone, X } from 'lucide-vue-next';
import { ofetch } from 'ofetch';
import { computed, ref } from 'vue';

const {
    csrf_token,
} = useCSRF();

const {
    webauthnKeys,
} = defineProps<{
    webauthnKeys: WebauthnKey[];
}>();

const confirmingKeyRegistration = ref(false);
const mfaVerificationToken = ref<string>("");
const keyNameInput = ref<string>("");

const getPublicKey = async () => {
    return await ofetch(route('profile.webauthn.store.options'), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf_token.value,
        },
    });
}

const storeNewKey = async () => {
    confirmingKeyRegistration.value = false;

    const key = await getPublicKey();
    const webauthn = new WebAuthn();
    const credential = await webauthn.register(key.publicKey, keyNameInput.value);
    await ofetch(route('profile.webauthn.store'), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf_token.value,
            'Content-Type': 'application/json',
            'X-MFA-VERIFICATION-TOKEN': mfaVerificationToken.value,
        },
        body: credential,
    });

    router.reload();
}

const keyIdToInspect = ref<number | null>(null);

const keyInspected = computed(() => {
    return webauthnKeys.find(k => k.id === keyIdToInspect.value) || null;
});

const mfaChallengeOpened = ref(false);

const deleteKey = async () => {
    let keyId = keyIdToInspect.value;

    if (keyId === null) {
        return;
    }

    keyIdToInspect.value = null;
    mfaChallengeOpened.value = false;

    await ofetch(route('profile.webauthn.destroy', { id: keyId }), {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf_token.value,
            'X-MFA-VERIFICATION-TOKEN': mfaVerificationToken.value,
        },
    });

    router.reload();
}

const mfaSolved = (token: string) => {
    mfaVerificationToken.value = token;
    mfaChallengeOpened.value = false;
    if (!!keyIdToInspect.value) {
        return deleteKey();
    }
    confirmingKeyRegistration.value = true;
};
</script>

<template>
    <div>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Multi-Factor Authentication
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Add additional security to your account using
                two-factor authentication.
            </p>
        </header>


        <div class="flex w-full h-15 mt-4">
            <div class="flex-1">
                <InputLabel class="py-2" value="Security Key / Key Chain" />

                <div v-if="webauthnKeys && webauthnKeys.length > 0" class="mt-2 space-y-2">
                    <ul class="flex flex-col gap-2">
                        <li v-for="key in webauthnKeys" :key="key.id" @click="keyIdToInspect = key.id"
                            class="flex items-center gap-5 text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded-md">
                            <Smartphone />
                            {{ key.name }}
                        </li>
                    </ul>
                </div>
                <div v-else class="mt-2 text-gray-700 dark:text-gray-300">
                    <p>No security keys registered.</p>
                </div>
                <PrimaryButton class="mt-4" @click="mfaChallengeOpened = true">Add Security Key</PrimaryButton>

            </div>
            <!-- <div>
                <Separator orientation="vertical" class="mx-4" />
            </div>
            <div class="flex-1">
                <InputLabel class="py-2" value="TOTP" />
            </div> -->
        </div>

        <Modal :show="confirmingKeyRegistration" @close="confirmingKeyRegistration = false">
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
                        placeholder="Key Name" required @keyup.enter="storeNewKey" autofocus />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="confirmingKeyRegistration = false">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton class="ms-3" @click="storeNewKey">
                        Register Key
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <Modal :show="keyIdToInspect != null" @close="keyIdToInspect = null">
            <div class="w-full flex justify-end p-4">
                <Button @click="keyIdToInspect = null" variant="outline" size="icon"
                    class="absolute rounded-full aspect-square border-gray-300 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200">
                    <X :size="20" />
                </Button>
            </div>
            <div class="p-6 pt-1">
                <div class="flex gap-3 items-center pb-5">
                    <Smartphone />
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ keyInspected?.name }}
                    </h2>
                </div>

                <p class="py-4 text-sm">
                    <CalendarDays class="inline-block mr-1 mb-1" :size="16" />
                    This key was registered on
                    {{ new Date(keyInspected?.created_at || '').toLocaleDateString(undefined, {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    }) }}.
                </p>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    <ShieldCheck class="inline-block mr-1 mb-1" :size="16" />
                    This security key can be used to authenticate strongly to your account.
                    You can use it during the login process or when performing sensitive actions.
                </p>

                <div class="mt-6 flex justify-end">

                    <DangerButton class="ms-3" @click="mfaChallengeOpened = true">
                        Delete
                    </DangerButton>
                </div>
            </div>
        </Modal>
        <MFAChallenge v-model:opened="mfaChallengeOpened" @verified="mfaSolved" />
    </div>
</template>
