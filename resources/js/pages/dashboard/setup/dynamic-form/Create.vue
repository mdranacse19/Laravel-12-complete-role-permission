<script setup lang="ts">
import { ref, onMounted, onUpdated, useAttrs, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import FormElement from './components/FormElement.vue';
import Preview from './components/Preview.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from 'primevue/button';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import RadioButton from 'primevue/radiobutton';
import RadioButtonGroup from 'primevue/radiobuttongroup';
import Skeleton from 'primevue/skeleton';
import Message from 'primevue/message';
import { Icon } from '@iconify/vue';
import Draggable from 'vuedraggable';
import { useToast } from 'primevue/usetoast';
import { showFlashMessage, showAxiosErrors } from '@/Helpers/toast';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { index as formsIndex, create as formsCreate, store as formsStore, update as formsUpdate, inputs as formsInputs } from '@/routes/setup/dynamic-form';
import { label } from '@primeuix/themes/aura/metergroup';

// Constants
const DEFAULT_STATUS = "1";
const DEFAULT_OPTIONS = 'Option 1\nOption 2\nOption 3';
const TOAST_SUCCESS_LIFE = 3000;
const TOAST_ERROR_LIFE = 5000;

// Types & Interfaces
interface FlashMessages {
    success?: string;
    error?: string;
    message?: string;
    warning?: string;
}

interface AvailableElement {
    id: string | number;
    type: string;
    label: string;
    icon: string;
}

interface TypeData {
    option: string;
    value: string;
}

interface FormElement {
    id: string;
    input_id: string | number;
    type: string;
    label: string;
    placeholder: string;
    required: boolean;
    has_action: boolean;
    options: string;
}

interface EditData {
    form_id: string;
    type: string;
    name: string;
    status: string;
    elements: FormElement[];
}

interface Props {
    types?: TypeData[]; // available types for the Select (optional)
    editData?: EditData;
    availableElements?: AvailableElement[];
}

// Props
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Forms',
        href: formsIndex().url,
    },
    {
        title: 'Create Form',
        href: formsCreate().url,
    },
];

// Composables
const attrs = useAttrs();
const toast = useToast();
const isProcessing = ref<boolean>(false);

// Reactive State
const isLoading = ref<boolean>(true);
const previewMode = ref<boolean>(false);
const draggedElement = ref<AvailableElement | null>(null);
const formElements = ref<FormElement[]>([]);
const availableElements = ref<AvailableElement[]>(props.availableElements || []);
const types = ref<TypeData[]>(props.types || []);

// Form Setup
const form = useForm({
    form_id: props.editData?.form_id || '',
    type: props.editData?.type || '',
    name: props.editData?.name || '',
    status: props.editData?.status || DEFAULT_STATUS,
    elements: [] as FormElement[],
});

// Initialize form elements if editing
if (props.editData?.elements) {
    formElements.value = props.editData.elements;
}

// Lifecycle Hooks
onMounted(async () => {
    if (!props.availableElements || props.availableElements.length === 0) {
        await fetchAvailableElements();
    }
    isLoading.value = false;
});

onUpdated(() => {
    showFlashMessage(toast, attrs.flash as FlashMessages);
});

// Helper Functions
const generateId = (): string => {
    return Math.random().toString(36).substring(2, 11);
};

const hasOptions = (type: string): boolean => {
    return ['select', 'multiSelect', 'radio', 'checkbox'].includes(type);
};

// Element Management Functions
const createElement = (element: AvailableElement): FormElement => {
    const type = element.type;
    const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);

    return {
        id: generateId(),
        input_id: element.id,
        type,
        label: `${capitalizedType} Field`,
        placeholder: `Enter ${type}`,
        required: false,
        has_action: false,
        options: hasOptions(type) ? DEFAULT_OPTIONS : '',
    };
};

const getElementIndex = (elementId: string): number => {
    return formElements.value.findIndex((el: FormElement) => el.id === elementId);
};

const handleDragStart = (element: AvailableElement, event: DragEvent): void => {
    draggedElement.value = element;
    if (event.dataTransfer) {
        event.dataTransfer.effectAllowed = 'copy';
        event.dataTransfer.setData('text/plain', element.type);
    }
};

const handleDragOver = (event: DragEvent): void => {
    event.preventDefault();
    if (event.dataTransfer) {
        event.dataTransfer.dropEffect = 'copy';
    }
};

const handleDrop = (event: DragEvent): void => {
    event.preventDefault();
    if (draggedElement.value) {
        const newElement = createElement(draggedElement.value);
        formElements.value.push(newElement);
        draggedElement.value = null;
    }
};

const updateElement = (id: string, field: string, value: any): void => {
    const index = formElements.value.findIndex((el: FormElement) => el.id === id);
    if (index !== -1) {
        (formElements.value[index] as any)[field] = value;
    }
};

const removeElement = (id: string): void => {
    formElements.value = formElements.value.filter((el: FormElement) => el.id !== id);
};

// Preview & Submission Functions
const togglePreviewMode = (): void => {
    previewMode.value = !previewMode.value;
};

const submitPreviewForm = (): void => {
    toast.add({
        severity: 'info',
        summary: 'Preview',
        detail: 'Form preview submitted! Check console for data.',
        group: 'br',
        life: TOAST_SUCCESS_LIFE,
    });
    console.log('Preview Form Data:', formElements.value);
};

const submitUrl = computed(() => {
    return props.editData?.form_id ? formsUpdate(props.editData.form_id).url : formsStore().url;
});

const submitMethod = computed(() => (props.editData?.form_id ? 'put' : 'post'));

const onSubmit = (): void => {
    if (formElements.value.length === 0) {
        toast.add({
            severity: 'warn',
            summary: 'Warning',
            detail: 'Please add at least one form element before submitting.',
            group: 'br',
            life: TOAST_ERROR_LIFE,
        });
        return;
    }

    form.elements = formElements.value;
    isProcessing.value = true;
    const options = {
        preserveScroll: true,
        onSuccess: (res: any) => {
            if (res.props?.flash?.success) {
                toast.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: res.props.flash.success,
                    group: 'br',
                    life: TOAST_SUCCESS_LIFE,
                });
            }
            isProcessing.value = false;
        },
        onError: (errors: any) => {
            for (const key in errors) {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: errors[key],
                    group: 'br',
                    life: TOAST_ERROR_LIFE,
                });
            }
            isProcessing.value = false;
        },
        onFinish: () => {
            isProcessing.value = false;
        },
    };

    if (submitMethod.value === 'post') {
        form.post(submitUrl.value, options);
    } else {
        form.put(submitUrl.value, options);
    }
};

// API Functions
const fetchAvailableElements = async (): Promise<void> => {
    try {
        const response = await fetch(formsInputs().url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.data) {
            availableElements.value = data.data;
        } else if (Array.isArray(data)) {
            availableElements.value = data;
        }
    } catch (error: any) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to fetch available elements',
            group: 'br',
            life: TOAST_ERROR_LIFE,
        });
        console.error('Failed to fetch available elements:', error);
    }
};
</script>
<template>
    <Head title="Form Builder" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageHeader>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Form Builder
            </h2>

            <Button as="a" label="Go Back" icon="pi pi-arrow-left" size="small" :href="formsIndex().url" />
        </template>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg pb-3">
            <div class="text-gray-900 dark:text-gray-100">
                <form @submit.prevent="onSubmit" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 px-6 pt-6">
                        <div>
                            <div v-if="isLoading" class="mb-2">
                                <Skeleton width="8rem" height="1.5rem" class="mb-2" />
                                <Skeleton width="100%" height="2.5rem" />
                            </div>
                            <div v-else>
                                <label for="type">Form Type <span class="text-red-500">*</span></label>
                                <Select
                                    fluid
                                    v-model="form.type"
                                    :options="types"
                                    optionLabel="option"
                                    optionValue="value"
                                    placeholder="Select a Type"
                                    class="w-full mt-1 md:w-56"
                                />
                                <Message v-if="form.errors.type" severity="error" variant="simple" size="small" class="mt-2">
                                    {{ form.errors.type }}
                                </Message>
                            </div>
                        </div>

                        <div>
                            <div v-if="isLoading" class="mb-2">
                                <Skeleton width="8rem" height="1.5rem" class="mb-2" />
                                <Skeleton width="100%" height="2.5rem" />
                            </div>
                            <div v-else>
                                <label for="name">Form Name <span class="text-red-500">*</span></label>
                                <InputText
                                    type="text"
                                    id="name"
                                    class="mt-1 block w-full"
                                    placeholder="Enter Form Name"
                                    v-model="form.name"
                                />
                                <Message v-if="form.errors.name" severity="error" variant="simple" size="small" class="mt-2">
                                    {{ form.errors.name }}
                                </Message>
                            </div>
                        </div>

                        <div>
                            <div v-if="isLoading">
                                <Skeleton width="8rem" height="1.5rem" class="mb-2" />
                                <div class="flex gap-3 mt-3">
                                    <Skeleton width="1.25rem" height="1.25rem" class="mr-1" />
                                    <Skeleton width="4rem" height="1.5rem" class="mr-4" />
                                    <Skeleton width="1.25rem" height="1.25rem" class="mr-1" />
                                    <Skeleton width="5rem" height="1.5rem" />
                                </div>
                            </div>
                            <div v-else>
                                <label for="status">Status <span class="text-red-500">*</span></label>
                                <div class="flex flex-wrap gap-3 mt-3">
                                    <RadioButtonGroup name="ingredient" class="flex flex-wrap gap-4">
                                        <div class="flex items-center gap-2">
                                            <RadioButton v-model="form.status" inputId="status-active" name="status"
                                                     value="1" checked />
                                            <label for="status-active" class="ml-2">Active</label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <RadioButton v-model="form.status" inputId="status-inactive" name="status"
                                                     value="0" />
                                            <label for="status-inactive" class="ml-2">Inactive</label>
                                        </div>
                                    </RadioButtonGroup>
                                </div>
                                <Message v-if="form.errors.status" severity="error" variant="simple" size="small" class="mt-2">
                                    {{ form.errors.status }}
                                </Message>
                            </div>
                        </div>
                    </div>
                </form>

                <section class="mt-5 mx-8">
                    <div class="min-h-screen">
                        <div class="w-full mx-auto">
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                                <div v-if="!previewMode" class="lg:col-span-1">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Form Elements</h3>

                                        <div v-if="isLoading" class="space-y-2">
                                            <Skeleton v-for="i in 6" :key="i" width="100%" height="3rem" class="mb-2" />
                                            <Skeleton width="100%" height="2.5rem" class="mt-4" />
                                        </div>

                                        <div v-else class="space-y-2">
                                            <div v-for="element in availableElements"
                                                :key="element.type"
                                                class="flex items-center p-3 bg-gray-50 dark:bg-gray-600 rounded-md cursor-move hover:bg-gray-100 transition-colors border-l-4 border-blue-500"
                                                draggable="true"
                                                @dragstart="(event) => handleDragStart(element, event)">
                                                <Icon :icon="element.icon" class="w-5 h-5 text-gray-600 dark:text-white mr-3" />
                                                <span class="text-sm font-medium text-gray-700 dark:text-white pointer-events-none">
                                                    {{ element.label }}
                                                </span>
                                            </div>

                                            <div class="mt-4 p-3 bg-blue-50 dark:bg-gray-700 rounded-md">
                                                <p class="text-xs text-blue-700 dark:text-white">
                                                    <strong>Tip:</strong> Drag elements from here to the form builder area.
                                                </p>
                                            </div>

                                            <div class="flex items-center gap-3 mt-3">
                                                <Skeleton v-if="isProcessing" width="8rem" height="2.5rem" />
                                                <Button v-else
                                                    :disabled="formElements.length === 0"
                                                    class="w-full justify-center"
                                                    type="button"
                                                    label="Submit"
                                                    severity="info"
                                                    :loading="isProcessing"
                                                    @click="onSubmit"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div :class="previewMode ? 'lg:col-span-4' : 'lg:col-span-3'">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ previewMode ? 'Form Preview' : 'Form Builder' }}
                                                </h3>
                                            </div>
                                            <div v-if="!isLoading" class="flex items-center space-x-2">
                                                <Icon
                                                    class="cursor-pointer w-6 h-6"
                                                    @click="togglePreviewMode"
                                                    :icon="previewMode ? 'mdi:eye-off-outline' : 'ep:view'"
                                                />
                                            </div>
                                        </div>

                                        <div class="p-6 min-h-96"
                                            :class="{'border-2 border-dashed border-gray-300 flex items-center justify-center' : !previewMode && formElements.length === 0}"
                                            @dragover="handleDragOver"
                                            @drop="handleDrop">

                                            <div v-if="isLoading" class="space-y-4">
                                                <Skeleton width="100%" height="3rem" />
                                                <Skeleton width="100%" height="3rem" />
                                                <Skeleton width="100%" height="3rem" />
                                            </div>

                                            <div v-else-if="!previewMode && formElements.length === 0" class="text-center">
                                                <Icon icon="pepicons-print:plus" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                                                <p class="text-gray-500 text-lg mb-2">No form elements yet</p>
                                                <p class="text-gray-400 text-sm">
                                                    Drag and drop elements from the sidebar to build your form
                                                </p>
                                            </div>

                                            <div v-else :class="{'max-w-md mx-auto' : previewMode}">
                                                <template v-if="previewMode">
                                                    <Preview
                                                        v-for="(element, index) in formElements"
                                                        :key="element.id"
                                                        :element="element"
                                                        :index="index"
                                                    />
                                                </template>

                                                <template v-else>
                                                    <Draggable
                                                        v-model="formElements"
                                                        item-key="id"
                                                        class="space-y-4"
                                                        :animation="200"
                                                        ghost-class="ghost"
                                                    >
                                                        <template #header>
                                                            <div style="display:none"></div>
                                                        </template>
                                                        <template #item="{ element }">
                                                            <FormElement
                                                                :element="element"
                                                                :index="getElementIndex(element.id)"
                                                                :errors="form.errors"
                                                                @update="updateElement"
                                                                @remove="removeElement"
                                                                class="dark:bg-gray-700"
                                                            />
                                                        </template>
                                                        <template #footer>
                                                            <div style="display:none"></div>
                                                        </template>
                                                    </Draggable>
                                                </template>

                                                <Button
                                                    v-if="previewMode && formElements.length > 0"
                                                    class="w-full mt-3 justify-center"
                                                    type="button"
                                                    label="Submit"
                                                    severity="info"
                                                    @click="submitPreviewForm"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>


    </AppLayout>
</template>

<style scoped>
.ghost {
    opacity: 0.4;
}
</style>
