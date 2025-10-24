<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { Transition } from "vue";

const props = defineProps(["group"]);

const form = useForm({
    name: props.group.name,
});

const deleteGroup = () => {
    form.delete(route("groups.destroy", props.group.id));
};
</script>

<template>

    <Head :title="`${group.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Group
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                General Information
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Basic information about this group
                            </p>
                        </header>

                        <form @submit.prevent="form.put(route('groups.update', group.id))" class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="id" value="Group Id" />

                                <TextInput id="id" type="text" class="mt-1 block w-full" :model-value="group.id"
                                    disabled />
                            </div>

                            <div>
                                <InputLabel for="name" value="Name" />

                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required
                                    autocomplete="name" />

                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                                <DangerButton @click="deleteGroup" :disabled="form.processing">Delete</DangerButton>

                                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">
                                        Saved.
                                    </p>
                                </Transition>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
