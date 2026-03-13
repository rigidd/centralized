<script setup lang="ts">
import DashboardAppCard from "@/Components/DashboardAppCard.vue";
import DashboardCard from "@/Components/DashboardCard.vue";
import DashboardHero from "@/Components/DashboardHero.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ClientModel } from "@/models/ClientModel";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const user = usePage().props.auth.user;

const clients = computed(() => {
    return user.clients.filter((client: ClientModel) => client.display);
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <DashboardHero
                :user-name="user.name"
                :app-count="clients.length"
                :has-biometrics="user.has_webauthn_enabled"
            />
        </template>

        <div class="py-10">
            <div
                class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8"
            >
                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="space-y-6 lg:col-span-2">
                        <DashboardCard
                            title="Connected applications"
                            description="Quickly access services linked to your Centralized account."
                        >
                            <div
                                v-if="clients.length"
                                class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"
                            >
                                <DashboardAppCard
                                    v-for="client in clients"
                                    :key="client.id ?? client.name"
                                    :name="client.name"
                                    :picture="client.picture"
                                    :redirect-url="client.redirect_urls[0]?.url"
                                />
                            </div>
                            <div
                                v-else
                                class="flex flex-col items-start justify-center gap-3 rounded-lg border border-dashed border-gray-300 px-4 py-6 text-sm text-gray-600 dark:border-gray-700 dark:text-gray-300"
                            >
                                <p>
                                    No application connected yet.
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Once you link applications to Centralized, they will appear here for quick access.
                                </p>
                            </div>
                        </DashboardCard>

                        <DashboardCard
                            title="Recent activity"
                            description="A quick overview of important events in your account."
                        >
                            <p
                                class="text-sm text-gray-500 dark:text-gray-400"
                            >
                                A detailed history of sign-ins and sensitive actions will be available here soon.
                            </p>
                        </DashboardCard>
                    </div>

                    <div class="space-y-6">
                        <DashboardCard
                            title="Account security"
                            description="Keep an eye on the security status of your profile."
                        >
                            <dl class="space-y-3 text-sm">
                                <div
                                    class="flex items-center justify-between"
                                >
                                    <dt
                                        class="text-gray-500 dark:text-gray-400"
                                    >
                                        Connected applications
                                    </dt>
                                    <dd
                                        class="font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ clients.length }}
                                    </dd>
                                </div>
                                <div
                                    class="flex items-center justify-between"
                                >
                                    <dt
                                        class="text-gray-500 dark:text-gray-400"
                                    >
                                        Biometrics (security key)
                                    </dt>
                                    <dd
                                        class="font-medium"
                                        :class="
                                            user.has_webauthn_enabled
                                                ? 'text-emerald-600 dark:text-emerald-400'
                                                : 'text-amber-600 dark:text-amber-400'
                                        "
                                    >
                                        {{
                                            user.has_webauthn_enabled
                                                ? 'Enabled'
                                                : 'Not enabled'
                                        }}
                                    </dd>
                                </div>
                                <div
                                    class="flex items-center justify-between"
                                >
                                    <dt
                                        class="text-gray-500 dark:text-gray-400"
                                    >
                                        Multi-factor authentication
                                    </dt>
                                    <dd
                                        class="text-right text-gray-900 dark:text-gray-100"
                                    >
                                        <span class="block">
                                            Managed from your profile
                                        </span>
                                        <Link
                                            :href="route('profile.edit')"
                                            class="mt-1 inline-flex text-xs font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                        >
                                            Open security settings
                                        </Link>
                                    </dd>
                                </div>
                            </dl>
                        </DashboardCard>

                        <DashboardCard
                            title="Recommendations"
                            description="A few quick actions to strengthen your security."
                        >
                            <ul class="space-y-2 text-sm">
                                <li>
                                    <Link
                                        :href="route('profile.edit', { section: 'mfa' })"
                                        class="flex items-start gap-2 rounded-md p-2 transition-colors hover:bg-indigo-50 dark:hover:bg-indigo-950/40"
                                    >
                                        <span
                                            class="mt-1 h-2 w-2 rounded-full"
                                            :class="
                                                user.has_webauthn_enabled
                                                    ? 'bg-emerald-500'
                                                    : 'bg-amber-500'
                                            "
                                        />
                                        <span>
                                            <span
                                                class="font-medium text-gray-900 dark:text-gray-100"
                                            >
                                                Enable or review biometrics
                                            </span>
                                            <span
                                                class="block text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                Use a security key or
                                                biometric-capable device as a
                                                strong second factor for
                                                sensitive actions.
                                            </span>
                                        </span>
                                    </Link>
                                </li>
                            </ul>
                        </DashboardCard>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
