<script setup lang="ts">
import { computed, reactive, ref } from 'vue';
import Modal from '../Modal.vue';
import Button from '../ui/button/Button.vue';
import { useUser } from '@/Composables/useUser';
import { Mail, Smartphone, X } from 'lucide-vue-next';
import { ofetch } from 'ofetch';
import { useCSRF } from '@/Composables/useCSRF';
import InputLabel from '../InputLabel.vue';
import { InputOTP, InputOTPGroup, InputOTPSlot } from '../ui/input-otp';
import InputError from '../InputError.vue';
import PrimaryButton from '../PrimaryButton.vue';

const {
    mfa_methods,
} = useUser();

const {
    csrf_token
} = useCSRF();

const emits = defineEmits<{
    (e: 'verified', token: string): void;
}>();

const availableChannels = reactive({
    mail: {
        label: 'Email',
        icon: Mail,
    },
    sms: {
        label: 'SMS',
        icon: Smartphone,
    },
});

const channels = computed(() => {
    return Object.keys(availableChannels).filter((method) => mfa_methods.value.includes(method)) as (keyof typeof availableChannels)[];
})

const selectedChannel = ref<keyof typeof availableChannels>('mail');

const opened = ref(false);

const startChallenge = async (channel: keyof typeof availableChannels) => {
    selectedChannel.value = channel;
    opened.value = true;

    await ofetch(route('mfa_challenge.pin_code.send'), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf_token.value,
        },
        body: {
            channel,
        },
    });
};

const pinCode = ref('');
const error = ref('');

const loading = ref<boolean>(false);

const submit = async () => {
    if (pinCode.value.length !== 6) {
        error.value = 'The pin code must be 6 digits.';
        return;
    }
    error.value = '';
    loading.value = true;

    const response = await ofetch(route('mfa_challenge.pin_code.verify'), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf_token.value,
        },
        body: {
            pin_code: pinCode.value,
        },
    }).catch((err) => {
        loading.value = false;
        if (err?.data?.error) {
            error.value = 'The provided pin code is invalid. Please try again.';
        } else {
            error.value = 'An unexpected error occurred. Please try again.';
        }
    });

    loading.value = false;
    emits('verified', response.mfa_verification_token);
    selectedChannel.value = 'mail';
    pinCode.value = '';
    opened.value = false;
};
</script>

<template>
    <div>
        <div v-for="method in channels" :key="method" @click="startChallenge(method)"
            class="flex-1 cursor-pointer text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-md p-3">
            <component :is="availableChannels[method].icon" class="inline-block mr-2 mb-1" :size="20" />
            Send Pin Code via {{ availableChannels[method].label }}
        </div>

        <Modal :show="opened" :closeable="false" :max-width="'sm'">
            <div class="w-full flex justify-end p-4">
                <Button @click="opened = false" variant="outline" size="icon"
                    class="absolute rounded-full aspect-square border-gray-300 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200">
                    <X :size="20" />
                </Button>
            </div>
            <div class="p-6 pt-1">
                <div class="flex gap-3 items-center pb-5">
                    <component :is="availableChannels[selectedChannel].icon" :size="20" />
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ availableChannels[selectedChannel].label }} Pin Code Verification
                    </h2>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    A pin code has been sent to you via {{ availableChannels[selectedChannel].label }}.
                </p>

                <form @submit.prevent="submit">
                    <div class="py-10 px-5">
                        <div>
                            <InputLabel for="pinCode" value="Pin Code" />

                            <InputOTP id="form-otp-demo-pin" v-model="pinCode" :maxlength="6" :aria-invalid="!!error"
                                :class="{ 'mt-4': true, 'opacity-30': loading }" :disabled="loading">
                                <InputOTPGroup>
                                    <InputOTPSlot :index="0" />
                                    <InputOTPSlot :index="1" />
                                    <InputOTPSlot :index="2" />
                                    <InputOTPSlot :index="3" />
                                    <InputOTPSlot :index="4" />
                                    <InputOTPSlot :index="5" />
                                </InputOTPGroup>
                            </InputOTP>

                            <p v-show="!error && !loading" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Please enter the code to verify your identity.
                            </p>

                            <InputError class="mt-2" :message="error" />

                            <PrimaryButton class="mt-8" :class="{ 'opacity-25': loading }" :disabled="loading">
                                <span v-if="loading">Verifying...</span>
                                <span v-else>
                                    Submit
                                </span>
                            </PrimaryButton>
                        </div>
                    </div>


                    <div class="mt-4 flex items-center justify-end">


                    </div>
                </form>

            </div>
        </Modal>
    </div>
</template>
