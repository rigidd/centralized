<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
    name: string;
    picture?: string | null;
    redirectUrl?: string | null;
}>();

const initials = computed(() => {
    if (!props.name) {
        return "";
    }

    return props.name
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part: string) => part[0]?.toUpperCase() ?? "")
        .join("");
});
</script>

<template>
    <component
        :is="redirectUrl ? 'a' : 'div'"
        :href="redirectUrl || undefined"
        target="_blank"
        rel="noreferrer"
        class="flex flex-col items-center justify-center gap-2 rounded-xl bg-white px-5 py-4 text-center text-sm text-gray-900 shadow-sm ring-1 ring-gray-200 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-700"
    >
        <div
            class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700"
        >
            <img
                v-if="picture"
                :src="picture"
                :alt="`${name} picture`"
                class="h-14 w-14 object-contain"
            />
            <span
                v-else
                class="text-lg font-semibold text-gray-500 dark:text-gray-300"
            >
                {{ initials }}
            </span>
        </div>

        <div class="mt-2">
            <p class="font-medium">
                {{ name }}
            </p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Open application
            </p>
        </div>
    </component>
</template>

