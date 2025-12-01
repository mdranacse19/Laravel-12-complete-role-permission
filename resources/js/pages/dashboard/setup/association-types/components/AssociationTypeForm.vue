<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import Message from 'primevue/message';
import { useToast } from "primevue/usetoast";
import Skeleton from 'primevue/skeleton';
import DatePicker from 'primevue/datepicker';
import { index as associationTypesIndex, store as associationTypesStore, update as associationTypesUpdate } from '@/routes/setup/association-type';
import { z } from 'zod';

const TOAST_LIFE = { success: 3000, error: 5000, info: 3000 };

// Zod validation schema
const validationSchema = z.object({
    name: z.string().min(1, 'Name is required').max(255, 'Name must not exceed 255 characters'),
    description: z.string().optional(),
    appKey: z.string().max(255, 'App Key must not exceed 255 characters').optional(),
    validUntil: z.date().nullable().optional(),
    token: z.string().max(500, 'Token must not exceed 500 characters').optional(),
    isActive: z.boolean(),
});

interface AssociationType {
    id: number;
    name: string;
    description?: string;
    appKey?: string;
    validUntil?: string;
    token?: string;
    isActive: boolean;
}

const props = defineProps<{
    mode: 'create' | 'edit';
    association?: AssociationType | null;
}>();

const emit = defineEmits<{
    success: [];
    cancel: [];
}>();

const toast = useToast();
const isProcessing = ref(false);
const validationErrors = ref<Record<string, string>>({});

const form = useForm({
    name: props.association?.name ?? '',
    description: props.association?.description ?? '',
    appKey: props.association?.appKey ?? '',
    validUntil: props.association?.validUntil ? new Date(props.association.validUntil) : null,
    token: props.association?.token ?? '',
    isActive: props.association?.isActive ?? true,
});

const submitUrl = computed(() => {
    return props.mode === 'create'
        ? associationTypesStore().url
        : associationTypesUpdate(props.association?.id ?? 0).url;
});

const submitMethod = computed(() => props.mode === 'create' ? 'post' : 'put');

// Helper to show toast notification
const showToast = (severity: 'success' | 'error' | 'info', detail: string) => {
    toast.add({
        severity,
        summary: severity === 'error' ? 'Error' : severity === 'success' ? 'Success' : 'Info',
        detail,
        group: 'br',
        life: TOAST_LIFE[severity] || 3000
    });
};

// Validate form data
const validateForm = () => {
    validationErrors.value = {};
    const result = validationSchema.safeParse(form.data());

    if (!result.success) {
        result.error.issues.forEach((issue: any) => {
            const field = issue.path[0] as string;
            validationErrors.value[field] = issue.message;
        });
        const firstError = Object.values(validationErrors.value)[0] as string;
        showToast('error', firstError);
        return false;
    }
    return true;
};// Transform form data for submission
const transformFormData = () => ({
    ...form.data(),
    validUntil: form.validUntil ? form.validUntil.toISOString().split('T')[0] : null,
});

const handleSubmit = () => {
    if (isProcessing.value || !validateForm()) return;

    isProcessing.value = true;

    const options = {
        preserveScroll: true,
        onError: (errors: any) => {
            isProcessing.value = false;
            Object.values(errors).forEach((error: any) => showToast('error', error));
        },
        onSuccess: () => {
            isProcessing.value = false;
            validationErrors.value = {};
            emit('success');
        },
        onFinish: () => {
            isProcessing.value = false;
        },
    };

    const submitData = transformFormData();

    if (submitMethod.value === 'post') {
        form.transform(() => submitData).post(submitUrl.value, options);
    } else {
        form.transform(() => submitData).put(submitUrl.value, options);
    }
};

const handleCancel = () => {
    form.reset();
    emit('cancel');
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
            <div v-if="isProcessing" class="mb-2">
                <Skeleton width="30%" height="1rem" class="mb-2" />
                <Skeleton width="100%" height="2.5rem" />
            </div>
            <div v-else>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Name <span class="text-red-500">*</span>
                </label>
                <InputText
                    id="name"
                    v-model="form.name"
                    class="w-full"
                    placeholder="Enter association type name"
                    :invalid="!!validationErrors.name || !!form.errors.name"
                />
                <Message v-if="validationErrors.name || form.errors.name" severity="error" variant="simple" size="small" class="mt-2">
                    {{ validationErrors.name || form.errors.name }}
                </Message>
            </div>
        </div>

        <div>
            <div v-if="isProcessing" class="mb-2">
                <Skeleton width="30%" height="1rem" class="mb-2" />
                <Skeleton width="100%" height="6rem" />
            </div>
            <div v-else>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <Textarea
                    id="description"
                    v-model="form.description"
                    class="w-full"
                    rows="4"
                    placeholder="Enter description (optional)"
                    :invalid="!!validationErrors.description || !!form.errors.description"
                />
                <Message v-if="validationErrors.description || form.errors.description" severity="error" variant="simple" size="small" class="mt-2">
                    {{ validationErrors.description || form.errors.description }}
                </Message>
            </div>
        </div>

        <div>
            <div v-if="isProcessing" class="mb-2">
                <Skeleton width="30%" height="1rem" class="mb-2" />
                <Skeleton width="100%" height="2.5rem" />
            </div>
            <div v-else>
                <label for="appKey" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    App Key
                </label>
                <InputText
                    id="appKey"
                    v-model="form.appKey"
                    class="w-full"
                    placeholder="Enter association type app key"
                    :invalid="!!validationErrors.appKey || !!form.errors.appKey"
                />
                <Message v-if="validationErrors.appKey || form.errors.appKey" severity="error" variant="simple" size="small" class="mt-2">
                    {{ validationErrors.appKey || form.errors.appKey }}
                </Message>
            </div>
        </div>

        <div>
            <div v-if="isProcessing" class="mb-2">
                <Skeleton width="30%" height="1rem" class="mb-2" />
                <Skeleton width="100%" height="2.5rem" />
            </div>
            <div v-else>
                <label for="validUntil" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Valid Until
                </label>
                <DatePicker
                    id="validUntil"
                    v-model="form.validUntil"
                    class="w-full"
                    placeholder="Enter association type valid until"
                    :invalid="!!validationErrors.validUntil || !!form.errors.validUntil"
                    fluid
                    dateFormat="yy-mm-dd"
                />
                <Message v-if="validationErrors.validUntil || form.errors.validUntil" severity="error" variant="simple" size="small" class="mt-2">
                    {{ validationErrors.validUntil || form.errors.validUntil }}
                </Message>
            </div>
        </div>

        <div>
            <div v-if="isProcessing" class="mb-2">
                <Skeleton width="30%" height="1rem" class="mb-2" />
                <Skeleton width="100%" height="2.5rem" />
            </div>
            <div v-else>
                <label for="token" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Token
                </label>
                <InputText
                    id="token"
                    v-model="form.token"
                    class="w-full"
                    placeholder="Enter association type token"
                    :invalid="!!validationErrors.token || !!form.errors.token"
                />
                <Message v-if="validationErrors.token || form.errors.token" severity="error" variant="simple" size="small" class="mt-2">
                    {{ validationErrors.token || form.errors.token }}
                </Message>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <div v-if="isProcessing" class="flex items-center gap-2">
                <Skeleton width="1.25rem" height="1.25rem" />
                <Skeleton width="3rem" height="1rem" />
            </div>
            <template v-else>
                <Checkbox
                    id="isActive"
                    v-model="form.isActive"
                    :binary="true"
                />
                <label for="isActive" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Active
                </label>
            </template>
        </div>

        <div class="flex justify-end gap-2 pt-4">
            <div v-if="isProcessing" class="flex justify-end gap-2 w-full">
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
