<script setup lang="ts">
import { store as userStore, update as userUpdate } from '@/routes/users';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import { computed, watch } from 'vue';

import FormDropdown from '@/components/FormDropdown.vue';
import FormText from '@/components/FormText.vue';
import { useLoader } from '@/composables/useLoader';

interface User {
    id?: number;
    fullName: string;
    bengaliName?: string;
    email: string;
    role?: string;
    designation?: string;
    bnDesignation?: string;
    isActive: boolean;
}

const props = defineProps<{
    mode: 'create' | 'edit';
    user?: User | null;
    roles: any[];
}>();

const emit = defineEmits<{
    success: [];
    cancel: [];
}>();

const { hideRoute } = useLoader();

const form = useForm({
    fullName: props.user?.fullName ?? '',
    bengaliName: props.user?.bengaliName ?? '',
    emailAddress: props.user?.email ?? '',
    role: props.user?.role ?? null,
    designation: props.user?.designation ?? '',
    bnDesignation: props.user?.bnDesignation ?? '',
    isActive: props.user?.isActive ?? true,
});

const submitUrl = computed(() => {
    return props.mode === 'create'
        ? userStore().url
        : userUpdate(props.user?.id ?? 0).url;
});

const submitMethod = computed(() => (props.mode === 'create' ? 'post' : 'put'));

const handleSubmit = () => {
    const options = {
        preserveScroll: true,
        preserveState: true,
        onStart: () => {
            // Hide the route loader immediately when form submission starts
            hideRoute();
        },
        onSuccess: () => {
            form.reset();
            emit('success');
        },
        onError: () => {
            hideRoute();
        },
        onFinish: () => {
            // Ensure loader is hidden when request completes
            hideRoute();
        },
    };

    if (submitMethod.value === 'post') {
        form.post(submitUrl.value, options);
    } else {
        form.put(submitUrl.value, options);
    }
};

const handleCancel = () => {
    form.reset();
    form.clearErrors();
    emit('cancel');
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <div class="flex gap-4">
            <div class="flex-1">
                <FormText
                    id="fullName"
                    v-model="form.fullName"
                    label="Name"
                    :required="true"
                    :isProcessing="form.processing"
                    :error="form.errors.fullName"
                    placeholder="Enter full name"
                />
            </div>

            <div class="flex-1">
                <FormText
                    id="email"
                    v-model="form.emailAddress"
                    label="Email"
                    :required="true"
                    :isProcessing="form.processing"
                    :error="form.errors.emailAddress"
                    placeholder="Enter email address"
                />
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex-1">
                <FormText
                    id="bengaliName"
                    v-model="form.bengaliName"
                    label="Bangla Name"
                    :isProcessing="form.processing"
                    :error="form.errors.bengaliName"
                    placeholder="Enter bangla name (optional)"
                />
            </div>

            <div class="flex-1">
                <FormDropdown
                    id="role"
                    v-model="form.role"
                    :options="roles"
                    optionLabel="name"
                    optionValue="id"
                    label="Role"
                    placeholder="Select Role"
                    :required="true"
                    :isProcessing="form.processing"
                    :error="form.errors.role"
                />
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex-1">
                <FormText
                    id="designation"
                    v-model="form.designation"
                    label="Designation"
                    :isProcessing="form.processing"
                    :error="form.errors.designation"
                    placeholder="Enter designation (optional)"
                />
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex-1">
                <FormText
                    id="bnDesignation"
                    v-model="form.bnDesignation"
                    label="Bangla Designation"
                    :isProcessing="form.processing"
                    :error="form.errors.bnDesignation"
                    placeholder="Enter bangla designation (optional)"
                />
            </div>

            <div class="flex-1">
                <label
                    for="isActive"
                    class="mb-2 block text-sm font-medium text-gray-800 dark:text-gray-300"
                >
                    Status
                </label>
                <div class="flex items-center gap-2">
                    <Checkbox
                        id="isActive"
                        v-model="form.isActive"
                        :disabled="form.processing"
                        :binary="true"
                    />
                    <label
                        for="isActive"
                        class="cursor-pointer text-sm text-gray-700 dark:text-gray-300"
                    >
                        Active
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-4">
            <Button
                type="button"
                label="Cancel"
                severity="secondary"
                outlined
                @click="handleCancel"
                :disabled="form.processing"
            />
            <Button
                type="submit"
                :label="mode === 'create' ? 'Create' : 'Update'"
                severity="primary"
                :disabled="form.processing"
            />
        </div>
    </form>
</template>
