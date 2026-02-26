<script setup lang="ts">
import Checkbox from "@/Components/Checkbox.vue";
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AppSelector from "@/Components/AppSelector.vue";
import GroupSelector from "@/Components/GroupSelector.vue";
import { ClientModel } from "@/models/ClientModel";
import { GroupModel } from "@/models/GroupModel";
import { useForm } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const props = defineProps<{
    user: any;
    roleEditable: boolean;
    clients: ClientModel[];
    groups: GroupModel[];
}>();

// Store selected IDs locally
const selectedClientIds = ref<(string | number)[]>(
    props.user.clients.map((client: ClientModel) => client.id)
);

const selectedGroupIds = ref<(string | number)[]>(
    props.user.groups.map((group: GroupModel) => group.id)
);

// Convert IDs to names for form submission
const clientNames = computed(() => {
    return selectedClientIds.value
        .map((id) => props.clients.find((c) => c.id === id)?.name)
        .filter((name): name is string => name !== undefined);
});

const groupNames = computed(() => {
    return selectedGroupIds.value
        .map((id) => props.groups.find((g) => g.id === id)?.name)
        .filter((name): name is string => name !== undefined);
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: "",
    role: props.user.role,
    clients: clientNames.value,
    groups: groupNames.value,
});

// Watch for changes in selected IDs and update form
watch([clientNames, groupNames], ([newClientNames, newGroupNames]) => {
    form.clients = newClientNames;
    form.groups = newGroupNames;
});

const deleteUser = () => {
    form.delete(route("users.destroy", props.user.id));
};

const role = computed({
    get: () => form.role === "admin",
    set: (value) => {
        form.role = value ? "admin" : "user";
    },
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                General Information
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Basic information about this user
            </p>
        </header>

        <form
            @submit.prevent="form.put(route('users.update', user.id))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="id" value="User Id" />

                <TextInput
                    id="id"
                    type="text"
                    class="mt-1 block w-full"
                    :model-value="user.id"
                    disabled
                />
            </div>

            <div>
                <InputLabel for="email" value="User Email" />

                <TextInput
                    id="email"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="email"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div v-if="roleEditable">
                <InputLabel value="Role" />

                <Checkbox
                    v-model:checked="role"
                    name="role"
                    value="admin"
                    class="mt-1"
                    label="Administration rights"
                />
            </div>

            <div>
                <InputLabel value="Apps" />

                <div class="mt-3">
                    <AppSelector
                        :available-apps="clients"
                        v-model:selected-app-ids="selectedClientIds"
                    />
                </div>

                <InputError class="mt-2" :message="form.errors.clients" />
            </div>

            <div>
                <InputLabel value="Groups" />

                <div class="mt-3">
                    <GroupSelector
                        :available-groups="groups"
                        v-model:selected-group-ids="selectedGroupIds"
                    />
                </div>

                <InputError class="mt-2" :message="form.errors.groups" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <DangerButton @click="deleteUser" :disabled="form.processing"
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
