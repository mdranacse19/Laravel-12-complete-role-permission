import { useToast } from "primevue/usetoast";

const TOAST_LIFE = { success: 3000, error: 5000, info: 3000, warn: 3000 };

export type ToastSeverity = 'success' | 'error' | 'info' | 'warn';

export interface ToastOptions {
    severity?: ToastSeverity;
    summary?: string;
    detail: string;
    life?: number;
    group?: string;
}

export function useToastNotification() {
    const toast = useToast();

    /**
     * Show a toast notification
     * @param options - Toast options or just the detail message string
     * @param severity - Optional severity if first param is a string
     */
    const showToast = (
        options: ToastOptions | string,
        severity: ToastSeverity = 'info'
    ) => {
        let config: ToastOptions;

        if (typeof options === 'string') {
            config = {
                severity,
                detail: options,
            };
        } else {
            config = options;
        }

        const finalSeverity = config.severity || severity;

        toast.add({
            severity: finalSeverity,
            summary: config.summary || getSummaryForSeverity(finalSeverity),
            detail: config.detail,
            group: config.group || 'br',
            life: config.life || TOAST_LIFE[finalSeverity] || 3000,
        });
    };

    /**
     * Show a success toast
     * @param detail - Message to display
     * @param summary - Optional custom summary
     */
    const showSuccess = (detail: string, summary?: string) => {
        showToast({ severity: 'success', detail, summary });
    };

    /**
     * Show an error toast
     * @param detail - Message to display
     * @param summary - Optional custom summary
     */
    const showError = (detail: string, summary?: string) => {
        showToast({ severity: 'error', detail, summary });
    };

    /**
     * Show an info toast
     * @param detail - Message to display
     * @param summary - Optional custom summary
     */
    const showInfo = (detail: string, summary?: string) => {
        showToast({ severity: 'info', detail, summary });
    };

    /**
     * Show a warning toast
     * @param detail - Message to display
     * @param summary - Optional custom summary
     */
    const showWarning = (detail: string, summary?: string) => {
        showToast({ severity: 'warn', detail, summary });
    };

    /**
     * Get default summary text for severity
     */
    const getSummaryForSeverity = (severity: ToastSeverity): string => {
        const summaries: Record<ToastSeverity, string> = {
            success: 'Success',
            error: 'Error',
            info: 'Info',
            warn: 'Warning',
        };
        return summaries[severity];
    };

    return {
        showToast,
        showSuccess,
        showError,
        showInfo,
        showWarning,
    };
}
