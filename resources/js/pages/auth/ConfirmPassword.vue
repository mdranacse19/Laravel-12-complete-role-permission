<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from 'primevue/button';
import Password from 'primevue/password';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/password/confirm';
import { Form, Head } from '@inertiajs/vue3';
</script>

<template>
    <AuthLayout
        title="Confirm your password"
        description="This is a secure area of the application. Please confirm your password before continuing."
    >
        <Head title="Confirm password" />

        <Form
            v-bind="store.form()"
            reset-on-success
            v-slot="{ errors, processing }"
        >
            <div class="space-y-6">
                <div class="grid gap-2">
                    <label htmlFor="password" class="text-sm font-medium">Password</label>
                    <Password
                        id="password"
                        name="password"
                        fluid
                        inputClass="w-full"
                        required
                        autocomplete="current-password"
                        autofocus
                        :feedback="false"
                        toggleMask
                    />

                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center">
                    <Button
                        label="Confirm Password"
                        class="w-full"
                        :loading="processing"
                        data-test="confirm-password-button"
                    />
                </div>
            </div>
        </Form>
    </AuthLayout>
</template>
