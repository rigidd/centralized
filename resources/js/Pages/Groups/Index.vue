<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Table } from "@dootix-developer/inertiajs-tables-laravel-query-builder";
import { Head, Link } from "@inertiajs/vue3";

defineProps(["groups"]);
</script>

<template>
    <Head title="Groups" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Groups
            </h2>
            <div class="flex flex-1"></div>
            <Link :href="route('groups.create')">
                <PrimaryButton :href="route('groups.create')"
                    >Create Group</PrimaryButton
                >
            </Link>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Table :resource="groups">
                    <template #cell(actions)="{ item: group }">
                        <Link :href="route('groups.edit', group.id)"> Edit </Link>
                    </template>
                </Table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
