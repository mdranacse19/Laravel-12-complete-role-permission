import type { ToastServiceMethods } from 'primevue/toastservice';

interface AxiosErrorResponse {
    errors?: Record<string, string[]>;
    response?: {
        data: {
            message: string;
        };
    };
}

/**
 * Shows axios error based on response data.
 */
export const showAxiosErrors = ($toast: ToastServiceMethods, responseData: any) => {
    if ((responseData?.errors || responseData?.response?.data) == undefined) {
        $toast.add({ severity: 'error', summary: 'Error', detail: responseData, group: 'br', life: 10000 });
    }
    else if (responseData.errors) {
        for (const [key, item] of Object.entries(responseData.errors)) {
            $toast.add({ severity: 'warn', summary: 'Warning', detail: (item as string[])[0], group: 'br', life: 3000 });
        }
    } else if (responseData.response?.data?.message) {
        $toast.add({ severity: 'error', summary: 'Error', detail: responseData.response.data.message, group: 'br', life: 10000 });
    }
};

interface FlashMessages {
    success?: string;
    error?: string;
    message?: string;
    warning?: string;
}

/**
 * Shows flash message.
 */
export const showFlashMessage = ($toast: ToastServiceMethods, flash: FlashMessages) => {
    if (flash.success) {
        $toast.add({ severity: 'success', summary: 'Success', detail: flash.success, group: 'br', life: 10000 });
    }

    if (flash.error) {
        $toast.add({ severity: 'error', summary: 'Error', detail: flash.error, group: 'br', life: 10000 });
    }

    if (flash.message) {
        $toast.add({ severity: 'info', summary: 'Info', detail: flash.message, group: 'br', life: 10000 });
    }

    if (flash.warning) {
        $toast.add({ severity: 'warn', summary: 'Warning', detail: flash.warning, group: 'br', life: 10000 });
    }
};
