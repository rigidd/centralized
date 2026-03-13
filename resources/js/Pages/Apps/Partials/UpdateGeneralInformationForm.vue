<script setup lang="ts">
import Checkbox from "@/Components/Checkbox.vue";
import DangerButton from "@/Components/DangerButton.vue";

import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { Plus, Minus } from "lucide-vue-next";
import { computed } from "vue";

const props = defineProps<{
    client: any;
    mustVerifyEmail?: Boolean;
    status?: String;
    secret?: string;
}>();

const form = useForm({
    name: props.client.name,
    picture: props.client.picture,
    display: props.client.display,
    redirect_urls: props.client.redirect_urls?.length ? props.client.redirect_urls : [{ url: "", alias: "", icon: "" }],
});

const canRemoveUrl = (index: string | number) => {
    const idx = Number(index);
    if (form.redirect_urls.length === 1) return false;
    
    const nonEmptyUrls = form.redirect_urls.filter((r: any) => r.url && r.url.trim() !== '');
    
    if (nonEmptyUrls.length === 1 && form.redirect_urls[idx].url && form.redirect_urls[idx].url.trim() !== '') {
        return false;
    }
    
    return true;
};

const deleteApp = () => {
    form.delete(route("apps.destroy", props.client.id));
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                General Information
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Basic information about this app
            </p>
        </header>

        <form
            @submit.prevent="form.put(route('apps.update', client.id))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="id" value="Client Id" />

                <TextInput
                    id="id"
                    type="text"
                    class="mt-1 block w-full"
                    :model-value="client.id"
                    disabled
                />
            </div>

            <div>
                <InputLabel for="id" value="Client Secret" />

                <TextInput
                    id="id"
                    type="text"
                    class="mt-1 block w-full"
                    :model-value="'**********'"
                    disabled
                />
            </div>

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

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="picture" value="Picture URL" />

                <TextInput
                    id="picture"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.picture"
                />

                <InputError class="mt-2" :message="form.errors.picture" />
            </div>

            <div>
                <InputLabel value="Display" />

                <Checkbox
                    v-model:checked="form.display"
                    name="display"
                    class="mt-1"
                    label="Display in the dashboard"
                />
            </div>

            <div>
                <InputLabel value="Redirect URLs" />
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Authorized callback URLs for OAuth redirects
                </p>

                <div class="mt-3 space-y-3">
                    <div
                        v-for="(redirect, index) in form.redirect_urls"
                        :key="index"
                        class="group relative flex flex-col gap-3 p-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 hover:border-gray-400 dark:hover:border-gray-500 transition-colors"
                    >
                        <div class="flex items-start gap-3 w-full">
                            <div class="flex-1 space-y-3">
                                <div>
                                    <InputLabel :value="'URL '" class="text-xs" />
                                    <TextInput
                                        type="url"
                                        class="w-full"
                                        v-model="redirect.url"
                                        required
                                        placeholder="https://example.com/callback"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="((form.errors) as Record<string, string>)[`redirect_urls.${index}.url`]"
                                    />
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <InputLabel :value="'Alias (Optional)'" class="text-xs" />
                                        <TextInput
                                            type="text"
                                            class="w-full"
                                            v-model="redirect.alias"
                                            placeholder="My Custom App"
                                        />
                                        <InputError
                                            class="mt-1"
                                            :message="((form.errors) as Record<string, string>)[`redirect_urls.${index}.alias`]"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel :value="'Icon URL (Optional)'" class="text-xs" />
                                        <TextInput
                                            type="url"
                                            class="w-full"
                                            v-model="redirect.icon"
                                            placeholder="https://example.com/icon.png"
                                        />
                                        <InputError
                                            class="mt-1"
                                            :message="((form.errors) as Record<string, string>)[`redirect_urls.${index}.icon`]"
                                        />
                                    </div>
                                </div>
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
                </div>

                <button
                    type="button"
                    class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    @click="form.redirect_urls.push({ url: '', alias: '', icon: '' })"
                >
                    <Plus :size="16" />
                    Add Redirect URL
                </button>

                <InputError class="mt-2" :message="form.errors.redirect_urls" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <DangerButton @click="deleteApp" :disabled="form.processing"
                    >Delete</DangerButton
                >

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
