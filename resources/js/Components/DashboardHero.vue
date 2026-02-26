<script setup lang="ts">
defineProps<{
    userName: string;
    appCount: number;
    hasBiometrics: boolean;
}>();
</script>

<template>
    <section
        class="w-full overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-sky-500 to-emerald-500 px-6 py-6 text-white shadow-sm sm:px-8 sm:py-7"
    >
        <div
            class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
        >
            <div class="max-w-xl">
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-white/70"
                >
                    Dashboard
                </p>
                <h1 class="mt-1 text-2xl font-semibold sm:text-3xl">
                    Hi, {{ userName }}
                </h1>
                <p class="mt-2 text-sm text-white/85">
                    Get a quick overview of your connected applications and your Centralized account status.
                </p>

                <div class="mt-4 flex flex-wrap gap-3 text-xs">
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1"
                    >
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-emerald-300 shadow-sm"
                        />
                        <span class="font-medium">
                            {{ appCount }}
                            connected application{{ appCount === 1 ? "" : "s" }}
                        </span>
                    </span>
                </div>
            </div>

            <div class="flex flex-col items-start gap-3 md:items-end">
                <div
                    class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-medium"
                    :class="
                        hasBiometrics
                            ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-100'
                            : 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-100'
                    "
                >
                    <span
                        class="h-1.5 w-1.5 rounded-full"
                        :class="
                            hasBiometrics ? 'bg-emerald-500' : 'bg-amber-500'
                        "
                    />
                    <span>
                        Account security:
                        {{ hasBiometrics ? "strong" : "needs attention" }}
                    </span>
                </div>

                <p
                    class="max-w-xs text-left text-xs text-white/80 md:text-right"
                >
                    Manage your authentication methods and active sessions from your Profile page to stay in control.
                </p>
            </div>
        </div>
    </section>
</template>

