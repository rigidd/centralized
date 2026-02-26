<script setup lang="ts">
import { computed } from "vue";
import { ClientModel } from "@/models/ClientModel";
import ItemSelector from "@/Components/ui/ItemSelector.vue";

const props = defineProps<{
    availableApps: ClientModel[];
    selectedAppIds: (string | number)[];
}>();

const emit = defineEmits<{
    (e: "update:selectedAppIds", value: (string | number)[]): void;
}>();

// Get initials for apps without picture
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
        :items="availableApps"
        :selected-item-ids="selectedAppIds"
        @update:selected-item-ids="emit('update:selectedAppIds', $event)"
        search-placeholder="Search by application name..."
        add-button-label="Add Application"
        modal-title="Add Applications"
        empty-state-text="No applications assigned"
        empty-state-subtext="Click the button below to add applications"
        no-results-text="No applications found matching your search"
    >
        <!-- Selected App Display -->
        <template #selected-item="{ item }">
            <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
                <img
                    v-if="item.picture"
                    :src="item.picture"
                    :alt="`${item.name} icon`"
                    class="h-12 w-12 object-contain"
                />
                <span
                    v-else
                    class="text-lg font-semibold text-gray-500 dark:text-gray-300"
                >
                    {{ getInitials(item.name) }}
                </span>
            </div>
            <p class="font-medium text-xs mt-1">
                {{ item.name }}
            </p>
        </template>

        <!-- Available App Display in Modal -->
        <template #available-item="{ item }">
            <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
                <img
                    v-if="item.picture"
                    :src="item.picture"
                    :alt="`${item.name} icon`"
                    class="h-12 w-12 object-contain"
                />
                <span
                    v-else
                    class="text-lg font-semibold text-gray-500 dark:text-gray-300"
                >
                    {{ getInitials(item.name) }}
                </span>
            </div>
            <p class="font-medium text-xs mt-1">
                {{ item.name }}
            </p>
        </template>
    </ItemSelector>
</template>
