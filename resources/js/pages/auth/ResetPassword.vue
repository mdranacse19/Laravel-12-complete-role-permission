<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <AuthLayout
        title="Reset password"
        description="Please enter your new password below"
    >
        <Head title="Reset password" />

        <Form
            v-bind="update.form()"
            :transform="(data) => ({ ...data, token, email })"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <label for="email" class="text-sm font-medium">Email</label>
                    <InputText
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="inputEmail"
                        class="w-full"
                        readonly
                    />
                    <InputError :message="errors.email" class="mt-2" />
                </div>

                <div class="grid gap-2">
                    <label for="password" class="text-sm font-medium">Password</label>
                    <Password
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        fluid
                        inputClass="w-full"
                        autofocus
                        placeholder="Password"
                        :feedback="false"
                        toggleMask
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <label for="password_confirmation" class="text-sm font-medium">
                        Confirm Password
                    </label>
                    <Password
                        id="password_confirmation"
                        name="password_confirmation"
                        autocomplete="new-password"
                        fluid
                        inputClass="w-full"
                        placeholder="Confirm password"
                        :feedback="false"
                        toggleMask
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    label="Reset password"
                    class="w-full mt-4"
                    :loading="processing"
                    data-test="reset-password-button"
                />
            </div>
        </Form>
    </AuthLayout>
</template>
