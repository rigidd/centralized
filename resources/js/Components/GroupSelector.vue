<script setup lang="ts">
import { computed } from "vue";
import { GroupModel } from "@/models/GroupModel";
import ItemSelector from "@/Components/ui/ItemSelector.vue";

const props = defineProps<{
    availableGroups: GroupModel[];
    selectedGroupIds: (string | number)[];
}>();

const emit = defineEmits<{
    (e: "update:selectedGroupIds", value: (string | number)[]): void;
}>();

// Get initials for groups
const getInitials = (name: string) => {
    return name
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() ?? "")
        .join("");
};
</script>

<template>
    <ItemSelector
        :items="availableGroups"
        :selected-item-ids="selectedGroupIds"
        @update:selected-item-ids="emit('update:selectedGroupIds', $event)"
        search-placeholder="Search by group name..."
        add-button-label="Add Group"
        modal-title="Add Groups"
        empty-state-text="No groups assigned"
        empty-state-subtext="Click the button below to add groups"
        no-results-text="No groups found matching your search"
    >
        <!-- Selected Group Display -->
        <template #selected-item="{ item }">
            <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900 dark:to-purple-800 ring-1 ring-purple-300 dark:ring-purple-700">
                <span class="text-2xl font-bold text-purple-700 dark:text-purple-200">
                    {{ getInitials(item.name) }}
                </span>
            </div>
            <p class="font-medium text-xs mt-1">
                {{ item.name }}
            </p>
        </template>

        <!-- Available Group Display in Modal -->
        <template #available-item="{ item }">
            <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900 dark:to-purple-800 ring-1 ring-purple-300 dark:ring-purple-700">
                <span class="text-2xl font-bold text-purple-700 dark:text-purple-200">
                    {{ getInitials(item.name) }}
                </span>
            </div>
            <p class="font-medium text-xs mt-1">
                {{ item.name }}
            </p>
        </template>

        <!-- Custom empty state for groups -->
        <template #empty-state>
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                No groups assigned
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-500">
                Click the button below to add groups
            </p>
        </template>
    </ItemSelector>
</template>
