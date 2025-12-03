<script setup lang="ts">
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/user-password';
import { Form, Head } from '@inertiajs/vue3';

import HeadingSmall from '@/components/dashboard/HeadingSmall.vue';
import Button from 'primevue/button';
import Password from 'primevue/password';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Password settings',
        href: edit().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Password settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Update password"
                    description="Ensure your account is using a long, random password to stay secure"
                />

                <Form
                    v-bind="PasswordController.update.form()"
                    :options="{
                        preserveScroll: true,
                    }"
                    reset-on-success
                    :reset-on-error="[
                        'password',
                        'password_confirmation',
                        'current_password',
                    ]"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <label for="current_password" class="text-sm font-medium">Current password</label>
                        <Password
                            id="current_password"
                            name="current_password"
                            class="w-full"
                            autocomplete="current-password"
                            placeholder="Current password"
                            :feedback="false"
                            toggleMask
                            fluid
                            inputClass="w-full"
                        />
                        <InputError :message="errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <label for="password" class="text-sm font-medium">New password</label>
                        <Password
                            id="password"
                            name="password"
                            class="w-full"
                            autocomplete="new-password"
                            placeholder="New password"
                            :feedback="false"
                            toggleMask
                            fluid
                            inputClass="w-full"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <label for="password_confirmation" class="text-sm font-medium"
                            >Confirm password</label
                        >
                        <Password
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full"
                            autocomplete="new-password"
                            placeholder="Confirm password"
                            :feedback="false"
                            toggleMask
                            fluid
                            inputClass="w-full"
                        />
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            type="submit"
                            :loading="processing"
                            data-test="update-password-button"
                            label="Save password"
                        />

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
