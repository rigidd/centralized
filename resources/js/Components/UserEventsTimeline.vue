<script setup lang="ts">
import { computed } from 'vue';
import { KeyRound, LogIn, AppWindow } from 'lucide-vue-next';

const props = defineProps<{
    events: Array<{
        id: number;
        event_type: string;
        ip_address: string | null;
        user_agent: string | null;
        details: Record<string, any> | null;
        created_at: string;
    }>;
    hideHeader?: boolean;
}>();

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric'
    }).format(date);
};

const getEventIcon = (type: string) => {
    switch (type) {
        case 'login':
            return LogIn;
        case 'password_updated':
            return KeyRound;
        case 'app_accessed':
            return AppWindow;
        default:
            return LogIn;
    }
};

const getEventColor = (type: string) => {
    switch (type) {
        case 'login':
            return 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400';
        case 'password_updated':
            return 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400';
        case 'app_accessed':
            return 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
        default:
            return 'bg-gray-100 text-gray-600 dark:bg-gray-900/30 dark:text-gray-400';
    }
};

const getEventTitle = (event: any) => {
    switch (event.event_type) {
        case 'login':
            return 'User logged in';
        case 'password_updated':
            return 'Password updated';
        case 'app_accessed':
            return `Accessed ${event.details?.client_name || 'an application'}`;
        default:
            return 'Unknown event';
    }
};

const getEventDetails = (event: any) => {
    let parts = [];
    if (event.ip_address) parts.push(`IP: ${event.ip_address}`);
    return parts.join(' • ');
};
</script>

<template>
    <section>
        <header v-if="!hideHeader" class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Recent Activity
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                A timeline of recent events for this user.
            </p>
        </header>

        <div v-if="!events || events.length === 0" class="text-sm text-gray-500 italic py-4">
            No recent activity recorded.
        </div>

        <div v-else class="relative border-l border-gray-200 dark:border-gray-700 ml-3 space-y-6 pb-4">
            <div v-for="event in events.slice(0, 15)" :key="event.id" class="relative pl-6">
                <!-- Icon marker -->
                <div 
                    class="absolute -left-3.5 top-1 p-1.5 rounded-full ring-4 ring-white dark:ring-gray-800"
                    :class="getEventColor(event.event_type)"
                >
                    <component :is="getEventIcon(event.event_type)" class="w-4 h-4" />
                </div>
                
                <!-- Content -->
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ getEventTitle(event) }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ formatDate(event.created_at) }}
                    </span>
                    <span v-if="getEventDetails(event)" class="text-xs text-gray-400 dark:text-gray-500 mt-1 truncate">
                        {{ getEventDetails(event) }}
                    </span>
                </div>
            </div>
        </div>
    </section>
</template>
