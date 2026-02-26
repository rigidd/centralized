<script setup lang="ts" generic="T extends { id: string | number; name: string }">
import { ref, computed } from "vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = withDefaults(
    defineProps<{
        items: T[];
        selectedItemIds: (string | number)[];
        searchPlaceholder?: string;
        addButtonLabel?: string;
        modalTitle?: string;
        emptyStateText?: string;
        emptyStateSubtext?: string;
        noResultsText?: string;
    }>(),
    {
        searchPlaceholder: "Search...",
        addButtonLabel: "Add Item",
        modalTitle: "Select Items",
        emptyStateText: "No items selected",
        emptyStateSubtext: "Click the button below to add items",
        noResultsText: "No items found",
    }
);

const emit = defineEmits<{
    (e: "update:selectedItemIds", value: (string | number)[]): void;
}>();

const showModal = ref(false);
const searchQuery = ref("");

// Compute selected items from IDs
const selectedItems = computed(() => {
    return props.selectedItemIds
        .map((id) => props.items.find((item) => item.id === id))
        .filter((item): item is T => item !== undefined);
});

// Compute available items (not already selected)
const unselectedItems = computed(() => {
    return props.items.filter(
        (item) => !props.selectedItemIds.includes(item.id)
    );
});

// Filtered items based on search query
const filteredItems = computed(() => {
    if (!searchQuery.value.trim()) {
        return unselectedItems.value;
    }
    
    const query = searchQuery.value.toLowerCase().trim();
    return unselectedItems.value.filter((item) =>
        item.name.toLowerCase().includes(query)
    );
});

const openModal = () => {
    showModal.value = true;
    searchQuery.value = "";
};

const closeModal = () => {
    showModal.value = false;
    searchQuery.value = "";
};

const addItem = (item: T) => {
    emit("update:selectedItemIds", [...props.selectedItemIds, item.id]);
};

const removeItem = (itemId: string | number) => {
    emit("update:selectedItemIds", props.selectedItemIds.filter((id) => id !== itemId));
};
</script>

<template>
    <div class="space-y-4">
        <!-- Selected Items Display -->
        <div v-if="selectedItems.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <div
                v-for="item in selectedItems"
                :key="item.id"
                class="group relative flex flex-col items-center justify-center gap-2 rounded-xl bg-white px-4 py-4 text-center text-sm text-gray-900 shadow-sm ring-1 ring-gray-200 transition-all duration-200 dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-700"
            >
                <!-- Remove button -->
                <button
                    type="button"
                    @click="removeItem(item.id)"
                    class="absolute -top-2 -right-2 h-6 w-6 rounded-full bg-red-500 text-white opacity-0 transition-opacity group-hover:opacity-100 hover:bg-red-600 flex items-center justify-center shadow-md"
                    title="Remove item"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Item Content Slot -->
                <slot name="selected-item" :item="item">
                    <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
                        <span class="text-lg font-semibold text-gray-500 dark:text-gray-300">
                            {{ item.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                    <p class="font-medium text-xs mt-1">
                        {{ item.name }}
                    </p>
                </slot>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8 px-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl">
            <slot name="empty-state">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ emptyStateText }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-500">
                    {{ emptyStateSubtext }}
                </p>
            </slot>
        </div>

        <!-- Add Button -->
        <SecondaryButton
            type="button"
            @click="openModal"
            class="w-full sm:w-auto"
        >
            <slot name="add-button-icon">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </slot>
            {{ addButtonLabel }}
        </SecondaryButton>

        <!-- Modal -->
        <Modal :show="showModal" @close="closeModal" max-width="2xl">
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ modalTitle }}
                    </h3>
                    <button
                        type="button"
                        @click="closeModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Search Input -->
                <div class="mb-6">
                    <InputLabel for="search-items" :value="`Search ${modalTitle}`" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <TextInput
                            id="search-items"
                            v-model="searchQuery"
                            type="text"
                            class="block w-full pl-10"
                            :placeholder="searchPlaceholder"
                            autocomplete="off"
                        />
                    </div>
                </div>

                <!-- Items Grid -->
                <div class="max-h-96 overflow-y-auto px-1 py-1">
                    <div v-if="filteredItems.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <button
                            v-for="item in filteredItems"
                            :key="item.id"
                            type="button"
                            @click="addItem(item)"
                            class="relative flex flex-col items-center justify-center gap-2 rounded-xl bg-white px-4 py-4 text-center text-sm text-gray-900 shadow-sm ring-1 ring-gray-200 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:ring-blue-500 dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-700 dark:hover:ring-blue-400"
                        >
                            <!-- Item Content Slot -->
                            <slot name="available-item" :item="item">
                                <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
                                    <span class="text-lg font-semibold text-gray-500 dark:text-gray-300">
                                        {{ item.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <p class="font-medium text-xs mt-1">
                                    {{ item.name }}
                                </p>
                            </slot>
                        </button>
                    </div>

                    <!-- No Results -->
                    <div v-else class="text-center py-12">
                        <slot name="no-results">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ searchQuery ? noResultsText : 'All items are already selected' }}
                            </p>
                        </slot>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 flex justify-end">
                    <SecondaryButton type="button" @click="closeModal">
                        Close
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </div>
</template>
