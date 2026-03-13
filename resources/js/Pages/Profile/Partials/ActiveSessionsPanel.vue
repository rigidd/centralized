<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import MFAChallenge from '@/Components/MFAChallenge/MFAChallenge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { router } from '@inertiajs/vue3';
import { Monitor, Smartphone, LogOut } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    sessions: Array<{
        agent: { is_desktop: boolean; platform: string; browser: string };
        ip_address: string;
        is_current_device: boolean;
        last_active: string;
    }>;
}>();

const mfaChallengeOpened = ref(false);

const confirmLogout = () => {
    mfaChallengeOpened.value = true;
};

const logoutOtherBrowserSessions = (token: string) => {
    router.delete(route('profile.sessions.destroy'), {
        preserveScroll: true,
        headers: {
            'X-MFA-VERIFICATION-TOKEN': token,
        },
        onSuccess: () => {
            mfaChallengeOpened.value = false;
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Browser Sessions
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage and log out your active sessions on other browsers and devices.
            </p>
        </header>

        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
            If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.
        </p>

        <!-- Other Browser Sessions -->
        <div v-if="sessions.length > 0" class="mt-6 space-y-6">
            <div
                v-for="(session, i) in sessions"
                :key="i"
                class="flex items-center"
            >
                <div>
                    <Monitor v-if="session.agent.is_desktop" class="w-8 h-8 text-gray-500" />
                    <Smartphone v-else class="w-8 h-8 text-gray-500" />
                </div>

                <div class="ml-3">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ session.agent.platform ? session.agent.platform : 'Unknown' }} - {{ session.agent.browser ? session.agent.browser : 'Unknown' }}
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">
                            {{ session.ip_address }},

                            <span v-if="session.is_current_device" class="text-emerald-500 font-semibold">This device</span>
                            <span v-else>Last active {{ session.last_active }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center mt-6">
            <PrimaryButton @click="confirmLogout" class="flex gap-2">
                <LogOut class="w-4 h-4" />
                Log Out Other Browser Sessions
            </PrimaryButton>

            <!-- Removed ActionMessage Since Form was removed -->
        </div>

        <!-- MFA Challenge Modal -->
        <MFAChallenge 
            v-model:opened="mfaChallengeOpened" 
            @verified="logoutOtherBrowserSessions" 
        />
    </section>
</template>
