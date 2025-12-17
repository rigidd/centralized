<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import Spinner from '@/Components/Spinner.vue';
import { ChevronLeft, ShieldCheck } from 'lucide-vue-next';
import { InputOTP, InputOTPGroup, InputOTPSlot } from '@/Components/ui/input-otp';
import { REGEXP_ONLY_DIGITS } from 'vue-input-otp';
import { Field, FieldGroup } from '@/Components/ui/field';

const props = defineProps<{
    status: string;
    channel: string;
}>();

const page = usePage();

const loading = ref<boolean>(false);

const form = useForm({
    pin_code: '',
});

const submit = () => {
    form.post(route(''), {
        onFinish: (e) => {
            form.reset('password');
        },
        onError: () => {
            loading.value = false;
        },
        onBefore: () => {
            loading.value = true;
        },
    });
};
</script>

<template>
    <GuestLayout heading="Welcome back!" subheading="To continue, please log in to your account.">

        <Head title="Log in" />

        <div v-if="loading" class="flex flex-col items-center gap-2 py-5">
            <Spinner />
            <h3 class="dark:text-white">Loading...</h3>
        </div>

        <div v-else>

            <div>
                <div>
                    <InputLabel value="Pin Code Verification" />

                    <p class="pt-2 text-sm text-gray-600 dark:text-gray-400">
                        <ShieldCheck class="inline-block mr-1 mb-1" :size="16" />
                        A pin code has been sent to your {{ channel }}. Please enter it to continue.
                    </p>

                    <form id="form-otp-demo" class="space-y-6 w-sm" @submit="onSubmit">
                        <FieldGroup>
                            <Field :data-invalid="!!errors.length">
                                <FieldLabel for="form-otp-demo-pin">
                                    One-Time Password
                                </FieldLabel>
                                <InputOTP id="form-otp-demo-pin" v-bind="componentField" :maxlength="6">
                                    <InputOTPGroup>
                                        <InputOTPSlot :index="0" />
                                        <InputOTPSlot :index="1" />
                                        <InputOTPSlot :index="2" />
                                        <InputOTPSlot :index="3" />
                                        <InputOTPSlot :index="4" />
                                        <InputOTPSlot :index="5" />
                                    </InputOTPGroup>
                                </InputOTP>
                                <FieldDescription>
                                    Please enter the one-time password sent to your phone.
                                </FieldDescription>
                            </Field>
                        </FieldGroup>
                        <Button type="submit" form="form-otp-demo">
                            Submit
                        </Button>
                    </form>

                </div>


                <div class="mt-8 flex flex-col">

                    <Link :href="route('logout')" method="post"
                        class="mt-4 cursor-pointer text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        <ChevronLeft :size="16" class="inline-block" />
                        <a class="text-sm ms-1 ">Back to Login</a>
                    </Link>
                </div>
            </div>
        </div>

    </GuestLayout>
</template>
