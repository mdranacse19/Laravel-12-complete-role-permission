<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
            <FormText
                id="password"
                v-model="form.password"
                label="New Password"
                :required="true"
                :isProcessing="isProcessing"
                :error="errors.password || validationErrors.password"
                placeholder="Enter new password"
            />
        </div>

        <div>
            <FormText
                id="password_confirmation"
                v-model="form.password_confirmation"
                label="Confirm Password"
                :required="true"
                :isProcessing="isProcessing"
                :error="
                    errors.password_confirmation ||
                    validationErrors.password_confirmation
                "
                placeholder="Confirm new password"
            />
        </div>

        <div class="flex justify-end gap-2 pt-4">
            <Button
                type="button"
                label="Cancel"
                severity="secondary"
                outlined
                @click="$emit('cancel')"
                :disabled="isProcessing"
            />
            <Button
                type="submit"
                label="Change Password"
                severity="primary"
                :loading="isProcessing"
            />
        </div>
    </form>
</template>

<script setup lang="ts">
import FormText from '@/components/FormText.vue';
import { useFormValidation } from '@/composables/useFormValidation';
import { useToastNotification } from '@/composables/useToastNotification';
import { password as userPasswordUpdate } from '@/routes/users';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { ref } from 'vue';
import * as yup from 'yup';

const props = defineProps<{ userId: number | null }>();
const emit = defineEmits(['success', 'cancel']);

const { showError } = useToastNotification();
const { validationErrors, validateWithSchema, clearValidationErrors } =
    useFormValidation();

const isProcessing = ref(false);

const form = useForm({
    password: '',
    password_confirmation: '',
    user_id: props.userId ?? null,
});

const schema = yup.object({
    password: yup
        .string()
        .required('Password is required')
        .min(8, 'Password must be at least 8 characters'),
    password_confirmation: yup
        .string()
        .oneOf([yup.ref('password')], 'Passwords must match')
        .required('Please confirm the password'),
});

const errors = form.errors;

const handleSubmit = async () => {
    if (isProcessing.value) return;

    const isValid = await validateWithSchema(schema, form, {
        abortEarly: false,
    });
    if (!isValid) return;

    // Determine target user id
    const targetUserId = props.userId ?? form.data().user_id ?? null;
    if (!targetUserId) {
        showError('No user specified for password change.');
        return;
    }

    isProcessing.value = true;
    form.transform(() => ({ ...form.data() }));
    form.patch(userPasswordUpdate.patch(targetUserId).url, {
        onError: (errs: any) => {
            isProcessing.value = false;
            Object.values(errs).forEach((e: any) => showError(e));
        },
        onSuccess: () => {
            isProcessing.value = false;
            clearValidationErrors();
            emit('success');
        },
        onFinish: () => {
            isProcessing.value = false;
        },
    });
};
</script>
