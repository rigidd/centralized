<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import UpdateMFAForm from './Partials/UpdateMFAForm.vue';
import { WebauthnKey } from '@/models/WebauthnKey';
import { nextTick, onMounted, ref } from 'vue';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
    webauthnKeys: WebauthnKey[];
}>();

const highlightMfaCard = ref(false);

onMounted(() => {
    const page = usePage();
    const url = new URL(page.url, window.location.origin);
    const section = url.searchParams.get('section');

    if (section !== 'mfa') {
        return;
    }

    nextTick(() => {
        const runScroll = () => {
            const el = document.getElementById('multi-factor-authentication');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // Start highlight slightly after scroll to make the effect more noticeable.
            setTimeout(() => {
                highlightMfaCard.value = true;

                setTimeout(() => {
                    highlightMfaCard.value = false;
                }, 4000);
            }, 500);
        };

        const hasViewTransition =
            typeof (document as Document & { startViewTransition?: unknown })
                .startViewTransition === 'function';

        if (hasViewTransition) {
            setTimeout(runScroll, 350);
        } else {
            runScroll();
        }
    });
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    id="multi-factor-authentication"
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800 transition duration-700"
                    :class="highlightMfaCard
                        ? 'ring-2 ring-offset-2 ring-offset-gray-100 dark:ring-offset-gray-900 ring-indigo-500 bg-gradient-to-r from-indigo-500/10 via-sky-500/10 to-emerald-500/10 dark:from-indigo-500/20 dark:via-sky-500/20 dark:to-emerald-500/20'
                        : ''"
                >
                    <UpdateMFAForm :webauthn-keys="webauthnKeys" class="max-w-xl" />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
