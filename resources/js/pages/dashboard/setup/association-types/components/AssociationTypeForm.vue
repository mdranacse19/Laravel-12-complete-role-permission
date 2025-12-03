<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import Message from 'primevue/message';
import Skeleton from 'primevue/skeleton';
// import DatePicker from 'primevue/datepicker';
import { index as associationTypesIndex, store as associationTypesStore, update as associationTypesUpdate } from '@/routes/setup/association-type';
import * as yup from 'yup';
import { useToastNotification } from '@/composables/useToastNotification';
import { useFormValidation } from '@/composables/useFormValidation';
import DatePicker from '@/components/DatePicker.vue';

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

const { showError } = useToastNotification();
const { validationErrors, validateWithSchema, clearValidationErrors } = useFormValidation();
const isProcessing = ref(false);

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

// YUP validation schema
const validationSchema = yup.object({
    name: yup.string().required('Name is required').max(255, 'Name must not exceed 255 characters'),
    description: yup.string().optional(),
    appKey: yup.string().max(255, 'App Key must not exceed 255 characters').optional(),
    validUntil: yup.date().nullable().optional(),
    token: yup.string().max(500, 'Token must not exceed 500 characters').optional(),
    isActive: yup.boolean().required(),
});

// Validate form data
const validateForm = async () => {
    form.clearErrors(); // Clear Inertia form errors
    
    return await validateWithSchema(validationSchema, form, {
        showToastOnError: true,
        toastMessage: 'Something error found!',
        abortEarly: false
    });
};

const handleSubmit = async () => {
    if (isProcessing.value) return;
    
    const isValid = await validateForm();

    if (!isValid) return;

    isProcessing.value = true;

    const options = {
        preserveScroll: true,
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
        form.transform(() => ({...form.data()})).post(submitUrl.value, options);
    } else {
        form.transform(() => ({...form.data()})).put(submitUrl.value, options);
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
