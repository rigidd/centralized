<script setup lang="ts">

import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";
import { Plus, Minus } from "lucide-vue-next";

const query = computed(() => {
    return new URLSearchParams(window.location.search)
})

const form = useForm({
    name: "",
    picture: "",
    display: true,
    redirect_urls: [""],
});

const canRemoveUrl = (index: string | number) => {
    const idx = Number(index);
    if (form.redirect_urls.length === 1) return false;
    
    const nonEmptyUrls = form.redirect_urls.filter((url: string) => url && url.trim() !== '');
    
    if (nonEmptyUrls.length === 1 && form.redirect_urls[idx] && form.redirect_urls[idx].trim() !== '') {
        return false;
    }
    
    return true;
};

const submit = () => {
    form.post(route("apps.store"));
};

onMounted(() => {
    if (query.value.has("name"))
        form.name = query.value.get("name") as string
    if (query.value.has("picture"))
        form.picture = query.value.get("picture") as string
    if (query.value.has("redirect_urls"))
        form.redirect_urls = (query.value.get("redirect_urls") as string).split(",")
})
</script>

<template>
    <Head title="New App" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                New App
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <section>
                        <header>
                            <h2
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                General Information
                            </h2>

                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Basic information about this app
                            </p>
                        </header>

                        <form @submit.prevent="submit" class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="name" value="Name" />

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.name"
                                />
                            </div>

                            <div>
                                <InputLabel for="picture" value="Picture URL" />

                                <TextInput
                                    id="picture"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.picture"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.picture"
                                />
                            </div>

                            <div>
                                <InputLabel value="Redirect URLs" />
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Authorized callback URLs for OAuth redirects
                                </p>

                                <div class="mt-3 space-y-3">
                                    <div
                                        v-for="(url, index) in form.redirect_urls"
                                        :key="index"
                                        class="group relative flex items-center gap-3 p-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 hover:border-gray-400 dark:hover:border-gray-500 transition-colors"
                                    >
                                        <div class="flex-1">
                                            <TextInput
                                                type="url"
                                                class="w-full"
                                                v-model="form.redirect_urls[index]"
                                                placeholder="https://example.com/callback"
                                            />
                                            <InputError
                                                class="mt-1"
                                                :message="((form.errors) as Record<string, string>)[`redirect_urls.${index}`]"
                                            />
                                        </div>
                                        <button
                                            type="button"
                                            :disabled="!canRemoveUrl(index)"
                                            :class="[
                                                'flex-shrink-0 p-2 rounded-lg transition-colors',
                                                !canRemoveUrl(index)
                                                    ? 'text-gray-300 dark:text-gray-700 cursor-not-allowed opacity-50'
                                                    : 'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 cursor-pointer'
                                            ]"
                                            @click="canRemoveUrl(index) && form.redirect_urls.splice(index, 1)"
                                            :title="!canRemoveUrl(index) ? 'At least one valid URL is required' : 'Remove URL'"
                                        >
                                            <Minus :size="20" />
                                        </button>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    @click="form.redirect_urls.push('')"
                                >
                                    <Plus :size="16" />
                                    Add Redirect URL
                                </button>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    Create
                                </PrimaryButton>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
