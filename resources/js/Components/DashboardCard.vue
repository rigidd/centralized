<script setup lang="ts">
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <section
        class="w-full rounded-xl border border-gray-200 bg-white/80 p-5 shadow-sm ring-1 ring-black/5 backdrop-blur-sm dark:border-gray-700 dark:bg-gray-800/90"
    >
        <div class="mb-3 flex items-start justify-between gap-3">
            <div>
                <h3
                    v-if="title || $slots.title"
                    class="text-sm font-semibold text-gray-900 dark:text-gray-100"
                >
                    <slot name="title">
                        {{ title }}
                    </slot>
                </h3>
                <p
                    v-if="description || $slots.description"
                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                >
                    <slot name="description">
                        {{ description }}
                    </slot>
                </p>
            </div>

            <div v-if="$slots.actions" class="shrink-0">
                <slot name="actions" />
            </div>
        </div>

        <div class="text-sm text-gray-700 dark:text-gray-200">
            <slot />
        </div>
    </section>
</template>

