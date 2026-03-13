<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Spinner from "@/Components/Spinner.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { ClientModel } from "@/models/ClientModel";
import { computed, ref } from "vue";
import { User as UserIcon, Mail, Grid, Users } from "lucide-vue-next";

const page = usePage();

const props = defineProps<{
    status?: string;
    client: ClientModel;
    scopes?: string[];
    request?: Record<string, string | number | boolean>;
    authToken: string;
    state: string;
    redirectUri: string;
}>();

const loading = ref<boolean>(false);

const aliasInfo = computed(() => {
    return props.client.redirect_urls?.find((r) => r.url === props.redirectUri);
});

const displayName = computed(() => {
    return aliasInfo.value?.alias || props.client.name;
});

const displayIcon = computed(() => {
    return aliasInfo.value?.icon || props.client.picture;
});

const currentUser = computed(() => {
    return (page.props.auth as any)?.user;
});

/**
 * Map raw scope strings to human-readable labels with icons.
 */
const scopeIcons: Record<string, any> = {
    'openid': UserIcon,
    'profile': UserIcon,
    'email': Mail,
    'groups': Users,
};

const displayScopes = computed(() => {
    if (!props.scopes?.length) return [];

    return props.scopes.map((scope) => ({
        label: scope.description,
        icon: scopeIcons[scope.id] ?? Grid,
    }));
});
</script>

<template>
    <GuestLayout>
        <Head :title="`Authorize ${displayName}`" />

        <div v-if="loading" class="flex flex-col items-center gap-5 py-6">
            <Spinner class="dark:text-white" />
            <p class="text-sm text-gray-500 dark:text-gray-400">Redirecting...</p>
        </div>

        <div v-else class="flex flex-col items-center gap-0">

            <!-- App Icon & Name -->
            <div class="flex flex-col items-center gap-2 pb-5">
                <img
                    v-if="displayIcon"
                    :src="displayIcon"
                    :alt="`${displayName} icon`"
                    class="h-16 w-16 rounded-2xl object-cover shadow-sm border border-gray-200 dark:border-gray-600"
                />
                <div
                    v-else
                    class="h-16 w-16 rounded-2xl bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xl font-bold text-gray-500 dark:text-gray-300"
                >
                    {{ displayName.charAt(0).toUpperCase() }}
                </div>

                <div class="text-center">
                    <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ displayName }}</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ client.name }}</p>
                </div>
            </div>

            <div class="w-full border-t border-gray-200 dark:border-gray-700" />

            <!-- Wants access -->
            <div class="w-full py-4">
                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Wants access to your Centralized account
                </p>

                <!-- Scopes list -->
                <ul v-if="displayScopes.length" class="space-y-3">
                    <li
                        v-for="scope in displayScopes"
                        :key="scope.label"
                        class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300"
                    >
                        <component
                            :is="scope.icon"
                            class="h-4 w-4 shrink-0 text-gray-400 dark:text-gray-500"
                        />
                        {{ scope.label }}
                    </li>
                </ul>
            </div>

            <div class="w-full border-t border-gray-200 dark:border-gray-700" />

            <!-- Signed-in user identity chip -->
            <div class="py-4 flex justify-center w-full">
                <div
                    v-if="currentUser"
                    class="inline-flex items-center gap-2 rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300"
                >
                    <span class="h-5 w-5 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-[10px] font-bold text-gray-700 dark:text-gray-200 uppercase">
                        {{ currentUser.name?.charAt(0) }}
                    </span>
                    {{ currentUser.email }}
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex w-full items-center justify-between gap-3">
                <form
                    method="POST"
                    :action="route('passport.authorizations.deny')"
                    class="flex-1"
                >
                    <input type="hidden" name="_token" :value="page.props.auth.csrf_token" autocomplete="off" />
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" name="state" :value="state" />
                    <input type="hidden" name="client_id" :value="client.id" />
                    <input type="hidden" name="auth_token" :value="authToken" />
                    <SecondaryButton type="submit" class="w-full justify-center">
                        Cancel
                    </SecondaryButton>
                </form>

                <form
                    method="POST"
                    :action="route('passport.authorizations.approve')"
                    class="flex-1"
                >
                    <input type="hidden" name="_token" :value="page.props.auth.csrf_token" autocomplete="off" />
                    <input type="hidden" name="state" :value="state" />
                    <input type="hidden" name="client_id" :value="client.id" />
                    <input type="hidden" name="auth_token" :value="authToken" />
                    <PrimaryButton type="submit" class="w-full justify-center">
                        Authorize
                    </PrimaryButton>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
