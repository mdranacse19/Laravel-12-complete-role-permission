<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Divider from 'primevue/divider';
import InputMask from 'primevue/inputmask';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import Password from 'primevue/password';
import Select from 'primevue/select';
import Skeleton from 'primevue/skeleton';
import { computed, ref } from 'vue';
// import DatePicker from 'primevue/datepicker';
import { useFormValidation } from '@/composables/useFormValidation';
import { useToastNotification } from '@/composables/useToastNotification';
import { store as stakeholdersStore, update as stakeholdersUpdate } from '@/routes/profile/stakeholders';
import * as yup from 'yup';

interface Stakeholder {
    id: number;
    type: string;
    name: string;
    bnName: string;
    designation?: string;
    bnDesignation?: string;
    mobileNo?: string;
    email?: string;
    password?: string;
    isActive: boolean;
}

interface TypeData {
    label: string;
    value: string;
}

const props = defineProps<{
    mode: 'create' | 'edit';
    stakeholder?: Stakeholder | null;
    types?: TypeData[]
}>();

const emit = defineEmits<{
    success: [];
    cancel: [];
}>();

const { showError } = useToastNotification();
const { validationErrors, validateWithSchema, clearValidationErrors } = useFormValidation();
const isProcessing = ref(false);

const form = useForm({
    type: props.stakeholder?.type ?? '',
    name: props.stakeholder?.name ?? '',
    bnName: props.stakeholder?.bnName ?? '',
    designation: props.stakeholder?.designation ?? '',
    bnDesignation: props.stakeholder?.bnDesignation ?? '',
    mobileNo: props.stakeholder?.mobileNo ?? '',
    email: props.stakeholder?.email ?? '',
    password: '',
    isActive: props.stakeholder?.isActive ?? true,
});

const submitUrl = computed(() => {
    return props.mode === 'create'
        ? stakeholdersStore().url
        : stakeholdersUpdate(props.stakeholder?.id ?? 0).url;
});

const submitMethod = computed(() => props.mode === 'create' ? 'post' : 'put');

// YUP validation schema
const validationSchema = yup.object({
    name: yup.string()
        .required('Name is required')
        .max(255, 'Name must not exceed 255 characters'),
    bnName: yup.string()
        .optional()
        .max(255, 'Name must not exceed 255 characters'),
    designation: yup.string()
        .optional(),
    bnDesignation: yup.string()
        .optional(),
    mobileNo: yup.string()
        .required('Mobile is required')
        .max(11, 'Mobile not exceed 11 characters'),
    email: yup.string()
        .email("That doesn't look like a valid email")
        .required('Email is required'),
    password: props.mode === 'create'
        ? yup.string()
            .required('Password is required')
            .min(8, 'Password must be at least 8 characters long')
            .matches(/[a-z]/, 'Password must contain at least one lowercase letter')
            .matches(/[A-Z]/, 'Password must contain at least one uppercase letter')
            .matches(/\d/, 'Password must contain at least one number')
            .matches(/[!@#$%^&*(),.?":{}|<>]/, 'Password must contain at least one special character')
        : yup.string()
            .test('password-edit', 'Password must meet requirements', function (value: string | undefined) {
                if (!value) return true; // skip validation if empty
                return (
                    value.length >= 8 &&
                    /[a-z]/.test(value) &&
                    /[A-Z]/.test(value) &&
                    /\d/.test(value) &&
                    /[!@#$%^&*(),.?":{}|<>]/.test(value)
                );
            }),
    isActive: yup.boolean()
        .required(),
});

// Password validation rules
const passwordRequirements = computed(() => {
    const pwd = form.password;
    return [
        { met: pwd.length >= 8, label: 'At least 8 characters' },
        { met: /[a-z]/.test(pwd), label: 'Contains lowercase letter' },
        { met: /[A-Z]/.test(pwd), label: 'Contains uppercase letter' },
        { met: /[0-9]/.test(pwd), label: 'Contains number' },
        { met: /[!@#$%^&*(),.?":{}|<>]/.test(pwd), label: 'Contains special character' },
    ];
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
<style scoped>
.p-password-meter-text {
    display: none !important;
}
.p-password-strength,
.p-password-info {
    display: none !important;
}
</style>


<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <div class="flex gap-4">
            <div class="flex-1">
                <div v-if="isProcessing" class="mb-2">
                    <Skeleton width="8rem" height="1.5rem" class="mb-2" />
                    <Skeleton width="100%" height="2.5rem" />
                </div>
                <div v-else>
                    <label for="type">Type <span class="text-red-500">*</span></label>
                    <Select
                        fluid
                        v-model="form.type"
                        :options="types"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select a Type"
                        class="w-full mt-1 md:w-56"
                    />
                    <Message v-if="form.errors.type" severity="error" variant="simple" size="small" class="mt-2">
                        {{ form.errors.type }}
                    </Message>
                </div>
            </div>
            <div class="flex-1">

            </div>
        </div>
        <div class="flex gap-4">
            <div class="flex-1">
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
                        placeholder="Enter stakeholder name"
                        :invalid="!!validationErrors.name || !!form.errors.name"
                    />
                    <Message v-if="validationErrors.name || form.errors.name" severity="error" variant="simple" size="small" class="mt-2">
                        {{ validationErrors.name || form.errors.name }}
                    </Message>
                </div>
            </div>
            <div class="flex-1">
                <div v-if="isProcessing" class="mb-2">
                    <Skeleton width="30%" height="1rem" class="mb-2" />
                    <Skeleton width="100%" height="2.5rem" />
                </div>
                <div v-else>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Bangla Name
                    </label>
                    <InputText
                        id="bnName"
                        v-model="form.bnName"
                        class="w-full"
                        placeholder="Enter stakeholder name (Bangla)"
                        :invalid="!!validationErrors.bnName || !!form.errors.bnName"
                    />
                    <Message v-if="validationErrors.bnName || form.errors.bnName" severity="error" variant="simple" size="small" class="mt-2">
                        {{ validationErrors.bnName || form.errors.bnName }}
                    </Message>
                </div>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="flex-1">
                <div v-if="isProcessing" class="mb-2">
                    <Skeleton width="30%" height="1rem" class="mb-2" />
                    <Skeleton width="100%" height="2.5rem" />
                </div>
                <div v-else>
                    <label for="designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Designation
                    </label>
                    <InputText
                        id="designation"
                        v-model="form.designation"
                        class="w-full"
                        rows="4"
                        placeholder="Enter designation (optional)"
                        :invalid="!!validationErrors.designation || !!form.errors.designation"
                    />
                    <Message v-if="validationErrors.designation || form.errors.designation" severity="error" variant="simple" size="small" class="mt-2">
                        {{ validationErrors.designation || form.errors.designation }}
                    </Message>
                </div>
            </div>
            <div class="flex-1">
                <div v-if="isProcessing" class="mb-2">
                    <Skeleton width="30%" height="1rem" class="mb-2" />
                    <Skeleton width="100%" height="2.5rem" />
                </div>
                <div v-else>
                    <label for="bnDesignation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Bangla Designation
                    </label>
                    <InputText
                        id="bnDesignation"
                        v-model="form.bnDesignation"
                        class="w-full"
                        rows="4"
                        placeholder="Enter bangla designation (optional)"
                        :invalid="!!validationErrors.bnDesignation || !!form.errors.bnDesignation"
                    />
                    <Message v-if="validationErrors.bnDesignation || form.errors.bnDesignation" severity="error" variant="simple" size="small" class="mt-2">
                        {{ validationErrors.bnDesignation || form.errors.bnDesignation }}
                    </Message>
                </div>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="flex-1">
                <div v-if="isProcessing" class="mb-2">
                    <Skeleton width="30%" height="1rem" class="mb-2" />
                    <Skeleton width="100%" height="2.5rem" />
                </div>
                <div v-else>
                    <label for="appKey" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Mobile <span class="text-red-500">*</span>
                    </label>
                    <InputMask id="mobile" v-model="form.mobileNo" mask="01*********" class="mt-1 block w-full"
                            placeholder="01*********" autocomplete="mobile"
                            @keydown="(event: KeyboardEvent) => { if (event.key.length === 1 && isNaN(Number(event.key))) event.preventDefault(); }" />
                    <Message v-if="validationErrors.mobileNo || form.errors.mobileNo" severity="error" variant="simple" size="small" class="mt-2">
                        {{ validationErrors.mobileNo || form.errors.mobileNo }}
                    </Message>
                </div>
            </div>
            <div class="flex-1">
                <div v-if="isProcessing" class="mb-2">
                    <Skeleton width="30%" height="1rem" class="mb-2" />
                    <Skeleton width="100%" height="2.5rem" />
                </div>
                <div v-else>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <InputText
                        id="email"
                        v-model="form.email"
                        class="w-full"
                        placeholder="Enter email"
                        :invalid="!!validationErrors.email || !!form.errors.email"
                    />
                    <Message v-if="validationErrors.email || form.errors.email" severity="error" variant="simple" size="small" class="mt-2">
                        {{ validationErrors.email || form.errors.email }}
                    </Message>
                </div>
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex-1">
                <div v-if="isProcessing" class="mb-2">
                    <Skeleton width="30%" height="1rem" class="mb-2" />
                    <Skeleton width="100%" height="2.5rem" />
                </div>
                <div v-else>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Password <span v-if="mode === 'create'" class="text-red-500">*</span>
                    </label>
                    <Password
                        id="password"
                        v-model="form.password"
                        class="w-full text-xs "
                        :invalid="!!validationErrors.password || !!form.errors.password"
                        toggleMask
                        placeholder="Enter password"
                        fluid
                        showClear
                        promptLabel=""
                        weakLabel=""
                        mediumLabel=""
                        strongLabel=""
                    >


                        <template #header>
                            <!-- <div class="font-semibold text-xs mb-2">Password</div> -->
                        </template>
                        <template #footer>
                          <Divider />
                            <ul class="pl-2 my-0 leading-tight text-xs space-y-0.5">
                                <li
                                    v-for="(req, index) in passwordRequirements"
                                    :key="index"
                                    :class="req.met ? 'text-green-600' : 'text-gray-500'"
                                >
                                    <i :class="req.met ? 'pi pi-check-circle' : 'pi pi-circle'" class="mr-1"></i>
                                    {{ req.label }}
                                </li>
                            </ul>
                        </template>
                    </Password>
                    <Message v-if="validationErrors.password || form.errors.password" severity="error" variant="simple" size="small" class="mt-2">
                        {{ validationErrors.password || form.errors.password }}
                    </Message>
                </div>
            </div>
            <div class="flex-1">
                <div v-if="isProcessing" class="flex items-center justify-end gap-2 mt-10">
                    <Skeleton width="1.25rem" height="1.25rem" />
                    <Skeleton width="3rem" height="1rem" />
                </div>
                <template v-else>
                    <div class="flex items-center justify-end gap-2 mt-10">
                        <Checkbox
                            id="isActive"
                            v-model="form.isActive"
                            :binary="true"
                        />
                        <label for="isActive" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Active
                        </label>
                    </div>
                </template>
            </div>
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
