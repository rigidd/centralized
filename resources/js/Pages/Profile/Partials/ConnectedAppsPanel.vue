<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import MFAChallenge from '@/Components/MFAChallenge/MFAChallenge.vue';
import { Unplug } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    connectedApps: Array<{
        id: string;
        client_name: string;
        client_picture: string | null;
        created_at: string;
    }>;
}>();

const mfaChallengeOpened = ref(false);
const tokenToRevoke = ref<string | null>(null);

const confirmRevoke = (tokenId: string) => {
    tokenToRevoke.value = tokenId;
    mfaChallengeOpened.value = true;
};

const revokeToken = (mfaToken: string) => {
    if (!tokenToRevoke.value) return;

    router.delete(route('profile.tokens.destroy', tokenToRevoke.value), {
        preserveScroll: true,
        headers: {
            'X-MFA-VERIFICATION-TOKEN': mfaToken,
        },
        onSuccess: () => {
            mfaChallengeOpened.value = false;
            tokenToRevoke.value = null;
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Connected Applications
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                These third-party applications have an active connection to your account. You can revoke their access at any time.
            </p>
        </header>

        <!-- Connected Apps List -->
        <div v-if="connectedApps.length > 0" class="mt-6 space-y-6">
            <div
                v-for="app in connectedApps"
                :key="app.id"
                class="flex items-center justify-between"
            >
                <div class="flex items-center">
                    <img 
                        v-if="app.client_picture" 
                        :src="app.client_picture" 
                        class="w-10 h-10 rounded-full border border-gray-200 dark:border-gray-700 bg-white" 
                    />
                    <div 
                        v-else 
                        class="w-10 h-10 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 uppercase font-semibold"
                    >
                        {{ app.client_name.charAt(0) }}
                    </div>

                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ app.client_name }}
                        </div>
                        <div class="text-xs text-gray-500">
                            Connected {{ app.created_at }}
                        </div>
                    </div>
                </div>

                <PrimaryButton 
                    @click="confirmRevoke(app.id)" 
                    class="!bg-red-600 hover:!bg-red-500 flex gap-2"
                >
                    <Unplug class="w-4 h-4" />
                    Revoke Access
                </PrimaryButton>
            </div>
        </div>

        <div v-else class="mt-6">
            <p class="text-sm text-gray-500 italic">No third-party applications are currently connected.</p>
        </div>

        <MFAChallenge 
            v-model:opened="mfaChallengeOpened" 
            @verified="revokeToken" 
        />
    </section>
</template>
