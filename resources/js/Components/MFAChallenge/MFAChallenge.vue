<script setup lang="ts">
import { ChevronLeft, ShieldCheck } from 'lucide-vue-next';
import PinCodeChallenge from '@/Components/MFAChallenge/PinCodeChallenge.vue';
import WebAuthnChallenge from '@/Components/MFAChallenge/WebAuthnChallenge.vue';
import Modal from '../Modal.vue';

const opened = defineModel<boolean>('opened');

const emit = defineEmits<{
    (e: 'verified', token: string): void;
}>();

const submitChallengeToken = (token: string) => {
    emit('verified', token);
};
</script>

<template>
    <Modal :show="opened" :closeable="false">
        <div class="w-full flex justify-end p-4">
            <Button @click="opened = false" variant="outline" size="icon"
                class="absolute rounded-full aspect-square border-gray-300 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200">
                <X :size="20" />
            </Button>
        </div>
        <div class="p-6 pt-1">
            <div class="flex gap-3 items-center pb-5">
                <ShieldCheck class="text-green-500 inline-block mr-2" :size="24" />
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Multi Factor Authentication
                </h2>
            </div>

            <div>

                <div>
                    <div>

                        <p class="pt-2 text-sm text-gray-600 dark:text-gray-400">
                            Please complete the multi-factor authentication to continue.
                        </p>
                    </div>

                    <div class="mt-8 flex flex-col">

                        <WebAuthnChallenge @verified="submitChallengeToken" />

                        <PinCodeChallenge @verified="submitChallengeToken" />

                        <div @click="opened = false"
                            class="mt-4 text-center cursor-pointer text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            <ChevronLeft :size="16" class="inline-block" />
                            <a class="text-sm ms-1 ">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </Modal>
</template>
