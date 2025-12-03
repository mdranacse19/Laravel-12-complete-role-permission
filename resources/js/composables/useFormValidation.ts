import { ref } from 'vue';
import * as yup from 'yup';
import { useToastNotification } from './useToastNotification';

export interface ValidationOptions {
    showToastOnError?: boolean;
    toastMessage?: string;
    abortEarly?: boolean;
}

export function useFormValidation() {
    const { showError } = useToastNotification();
    const validationErrors = ref<Record<string, string>>({});

    /**
     * Extract and set validation errors from YUP validation error
     * @param error - The YUP validation error object
     * @param options - Options for error handling
     * @returns The errors object
     */
    const setValidationErrors = (
        error: any,
        options: ValidationOptions = {}
    ): Record<string, string> => {
        const {
            showToastOnError = true,
            toastMessage = 'Something error found!',
        } = options;

        const errors: Record<string, string> = {};

        if (error.inner && error.inner.length > 0) {
            error.inner.forEach((err: any) => {
                if (err.path) {
                    errors[err.path] = err.message;
                }
            });

            validationErrors.value = errors;

            if (showToastOnError) {
                showError(toastMessage);
            }
        }

        return errors;
    };

    /**
     * Validate form data against a YUP schema
     * @param schema - YUP validation schema
     * @param data - Data to validate
     * @param options - Validation options
     * @returns Promise<boolean> - True if valid, false if invalid
     */
    const validateWithSchema = async (
        schema: yup.ObjectSchema<any>,
        data: any,
        options: ValidationOptions = {}
    ): Promise<boolean> => {
        const { abortEarly = false } = options;

        validationErrors.value = {};

        try {
            await schema.validate(data, { abortEarly });
            return true;
        } catch (error: any) {
            setValidationErrors(error, options);
            return false;
        }
    };

    /**
     * Clear all validation errors
     */
    const clearValidationErrors = () => {
        validationErrors.value = {};
    };

    /**
     * Set a single field error
     * @param field - Field name
     * @param message - Error message
     */
    const setFieldError = (field: string, message: string) => {
        validationErrors.value[field] = message;
    };

    /**
     * Clear a single field error
     * @param field - Field name
     */
    const clearFieldError = (field: string) => {
        delete validationErrors.value[field];
    };

    /**
     * Check if a field has an error
     * @param field - Field name
     * @returns boolean
     */
    const hasFieldError = (field: string): boolean => {
        return !!validationErrors.value[field];
    };

    /**
     * Get error message for a specific field
     * @param field - Field name
     * @returns string | undefined
     */
    const getFieldError = (field: string): string | undefined => {
        return validationErrors.value[field];
    };

    return {
        validationErrors,
        setValidationErrors,
        validateWithSchema,
        clearValidationErrors,
        setFieldError,
        clearFieldError,
        hasFieldError,
        getFieldError,
    };
}
