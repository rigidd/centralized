<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    heading?: string;
    subheading?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-900"
    >
        <div>
            <Link href="/">
                <ApplicationLogo class="py-4 text-gray-500" />
            </Link>
        </div>

        <div
            v-if="heading || subheading"
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-lg sm:rounded-lg dark:bg-gray-800"
        >
            <div class="mt-2 mb-4">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ heading }}
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ subheading }}
                </p>
            </div>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-lg sm:rounded-lg dark:bg-gray-800"
        >
            <slot />
        </div>
    </div>
</template>
