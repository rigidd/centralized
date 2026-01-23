<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Spinner from '@/Components/Spinner.vue';
import { ChevronLeft, ShieldCheck } from 'lucide-vue-next';
import PinCodeChallenge from '@/Components/MFAChallenge/PinCodeChallenge.vue';
import WebAuthnChallenge from '@/Components/MFAChallenge/WebAuthnChallenge.vue';

const props = defineProps<{
    fromOauth?: boolean;
}>();

const loading = ref<boolean>(false);

const submitChallengeToken = () => {
    loading.value = true;

    if (props.fromOauth) {
        return router.reload();
    }

    router.visit(route('dashboard'));
};
</script>

<template>
    <GuestLayout heading="Welcome back!" subheading="To continue, please log in to your account.">

        <Head title="Log in" />

        <div v-if="loading" class="flex flex-col items-center gap-2 py-5">
            <Spinner />
            <h3 class="dark:text-white">Loading...</h3>
        </div>

        <div v-else>

            <div>
                <div>
                    <InputLabel value="Multi Factor Authentication" />

                    <p class="pt-2 text-sm text-gray-600 dark:text-gray-400">
                        <ShieldCheck class="inline-block mr-1 mb-1" :size="16" />
                        Please complete the multi-factor authentication to access your account.
                    </p>
                </div>

                <div class="mt-8 flex flex-col">

                    <WebAuthnChallenge @verified="submitChallengeToken" />

                    <PinCodeChallenge @verified="submitChallengeToken" />

                    <Link v-if="!fromOauth" :href="route('logout')" method="post"
                        class="mt-4 cursor-pointer text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        <ChevronLeft :size="16" class="inline-block" />
                        <a class="text-sm ms-1 ">Back to Login</a>
                    </Link>
                </div>
            </div>
        </div>

    </GuestLayout>
</template>
