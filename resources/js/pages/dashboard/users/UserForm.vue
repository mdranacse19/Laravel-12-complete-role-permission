<script setup lang="ts">
import { useFormValidation } from '@/composables/useFormValidation';
import { useToastNotification } from '@/composables/useToastNotification';
import { store as userStore, update as userUpdate } from '@/routes/users';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Skeleton from 'primevue/skeleton';
import { computed, ref } from 'vue';
import * as yup from 'yup';

import FormDropdown from '@/components/FormDropdown.vue';
import FormText from '@/components/FormText.vue';

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

const { showError } = useToastNotification();
const { validationErrors, validateWithSchema, clearValidationErrors } =
    useFormValidation();
const isProcessing = ref(false);

const form = useForm({
    fullName: props.user?.fullName ?? '',
    bengaliName: props.user?.bengaliName ?? '',
    emailAddress: props.user?.email ?? '',
    role: props.user?.role ?? null,
    designation: props.user?.designation ?? '',
    bnDesignation: props.user?.bnDesignation ?? '',
    isActive: !!props.user?.isActive,
});

const submitUrl = computed(() => {
    return props.mode === 'create'
        ? userStore().url
        : userUpdate(props.user?.id ?? 0).url;
});

const submitMethod = computed(() => (props.mode === 'create' ? 'post' : 'put'));

// YUP validation schema
const validationSchema = yup.object({
    fullName: yup
        .string()
        .required('Name is required')
        .max(255, 'Name must not exceed 255 characters'),
    bengaliName: yup
        .string()
        .optional()
        .max(255, 'Bangla Name must not exceed 255 characters'),
    emailAddress: yup
        .string()
        .required('Email is required')
        .email('Email must be a valid email address')
        .max(255, 'Email must not exceed 255 characters'),
    role: yup.number().required('Role is required'),
    designation: yup.string().optional(),
    bnDesignation: yup.string().optional(),
    isActive: yup.boolean().required(),
});

// Validate form data
const validateForm = async () => {
    form.clearErrors(); // Clear Inertia form errors

    return await validateWithSchema(validationSchema, form, {
        showToastOnError: true,
        toastMessage: 'Something error found!',
        abortEarly: false,
    });
};

const handleSubmit = async () => {
    if (isProcessing.value) return;

    const isValid = await validateForm();
    if (!isValid) return;

    isProcessing.value = true;

    const options = {
        preserveScroll: true,
        preserveState: true,
        onError: (errors: any) => {
            isProcessing.value = false;
            Object.values(errors).forEach((error: any) => showError(error));
        },
        onSuccess: () => {
            isProcessing.value = false;
            clearValidationErrors();
            emit('success');
        },
        onFinish: () => {
            isProcessing.value = false;
        },
    };

    if (submitMethod.value === 'post') {
        form.transform(() => ({ ...form.data() })).post(
            submitUrl.value,
            options,
        );
    } else {
        form.transform(() => ({ ...form.data() })).put(
            submitUrl.value,
            options,
        );
    }
};

const handleCancel = () => {
    form.reset();
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
                    :isProcessing="isProcessing"
                    :error="validationErrors.fullName || form.errors.fullName"
                    placeholder="Enter full name"
                />
            </div>

            <div class="flex-1">
                <FormText
                    id="email"
                    v-model="form.emailAddress"
                    label="Email"
                    :required="true"
                    :isProcessing="isProcessing"
                    :error="
                        validationErrors.emailAddress ||
                        form.errors.emailAddress
                    "
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
                    :isProcessing="isProcessing"
                    :error="
                        validationErrors.bengaliName || form.errors.bengaliName
                    "
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
                    :isProcessing="isProcessing"
                    :error="validationErrors.role || form.errors.role"
                />
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex-1">
                <FormText
                    id="designation"
                    v-model="form.designation"
                    label="Designation"
                    :isProcessing="isProcessing"
                    :error="
                        validationErrors.designation || form.errors.designation
                    "
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
                    :isProcessing="isProcessing"
                    :error="
                        validationErrors.bnDesignation ||
                        form.errors.bnDesignation
                    "
                    placeholder="Enter bangla designation (optional)"
                />
            </div>

            <div class="flex-1">
                <div v-if="isProcessing" class="flex items-center gap-2">
                    <Skeleton width="30%" height="2rem" />
                    <Skeleton width="100%" height="2rem" />
                </div>
                <div v-else>
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
                            :disabled="isProcessing"
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
        </div>

        <div class="flex justify-end gap-2 pt-4">
            <div v-if="isProcessing" class="flex w-full justify-end gap-2">
                <Skeleton width="5rem" height="2.5rem" />
                <Skeleton width="5rem" height="2.5rem" />
            </div>
            <template v-else>
                <Button
                    type="button"
                    label="Cancel"
                    severity="secondary"
                    outlined
                    @click="handleCancel"
                    :disabled="isProcessing"
                />
                <Button
                    type="submit"
                    :label="mode === 'create' ? 'Create' : 'Update'"
                    severity="primary"
                    :loading="isProcessing"
                />
            </template>
        </div>
    </form>
</template>
