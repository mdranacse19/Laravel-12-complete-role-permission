<script setup lang="ts">
// import InputLabel from "@/Components/InputLabel.vue";
import InputText from "primevue/inputtext";
import Checkbox from 'primevue/checkbox';
import { Icon } from "@iconify/vue";
import { ref } from "vue";
import Message from 'primevue/message';
import Textarea from 'primevue/textarea';

// Types & Interfaces
interface FormElement {
    id: string;
    type: string;
    label: string;
    placeholder: string;
    required: boolean;
    has_action: boolean;
    options: string;
}

interface Props {
    element: FormElement;
    errors?: Record<string, string>;
    index: number;
    previewMode?: boolean;
}

interface IconMap {
    [key: string]: string;
}

// Props & Emits
const props = defineProps<Props>();
const emit = defineEmits<{
    update: [id: string, field: string, value: any];
    remove: [id: string];
    reorder: [];
}>();

// Constants
const iconMap: IconMap = {
    text: 'system-uicons:write',
    email: 'mage:email',
    number: 'octicon:number-24',
    date: 'lets-icons:date-today-duotone',
    checkbox: 'proicons:checkbox-checked',
    radio: 'system-uicons:write',
    select: 'fluent:select-all-on-20-regular',
    textarea: 'carbon:text-long-paragraph',
};

// Reactive State
const isCollapsed = ref<boolean>(false);
const iconComponent = iconMap[props.element.type] || '';

// Functions
function emitUpdate(field: string, value: any): void {
    emit('update', props.element.id, field, value);
}

function onOptionChange(e: Event): void {
    const target = e.target as HTMLTextAreaElement;
    emitUpdate('options', target.value);
}

function handleCheckboxChange(field: string, event: Event): void {
    const target = event.target as HTMLInputElement;
    emitUpdate(field, target.checked);
}
</script>
<template>
    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <Icon icon="meteor-icons:grip-dots-vertical" class="w-4 h-4 text-gray-400 cursor-move"/>
                <Icon :icon="iconComponent" class="w-4 h-4 text-gray-600"/>
                <span class="text-sm font-medium text-gray-700">
                    {{ props.element.type.toUpperCase() }}
                    {{ props.element.label ? `(${props.element.label})` : '' }}
                </span>
            </div>
            <div class="flex items-center space-x-2">
                <!-- Collapse Toggle -->
                <button @click="isCollapsed = !isCollapsed" class="text-gray-500 hover:text-gray-700">
                    <Icon :icon="isCollapsed ? 'mdi:chevron-down' : 'mdi:chevron-up'" class="w-4 h-4" />
                </button>
                <button
                    @click="$emit('remove', props.element.id)"
                    class="text-red-500 hover:text-red-700">
                    <Icon icon="ion:trash-outline" class="w-4 h-4"/>
                </button>
            </div>
        </div>
        <div v-show="!isCollapsed" class="space-y-3">
            <div>
                <label :for="`label-${props.index}`"> Label </label>
                <InputText
                    :id="`label-${props.index}`"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="props.element.label"
                    @input="emitUpdate('label', props.element.label)"
                />
            </div>
            <div>
                <label :for="`placeholder-${props.index}`"> Placeholder </label>
                <InputText
                    :id="`placeholder-${props.index}`"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="props.element.placeholder"
                    @input="emitUpdate('placeholder', props.element.placeholder)"
                />
            </div>
            <div v-if="['select', 'multiSelect', 'radio', 'checkbox'].includes(props.element.type)">
                <label :for="`options-${props.index}`"> Options (Enter options line by line) </label>
                <Textarea
                    :id="`options-${props.index}`"
                    class="mt-1 block w-full"
                    placeholder="Enter options line by line"
                    v-model="props.element.options"
                    @input="onOptionChange"
                    rows="5"
                />
            </div>
            <div class="flex items-center gap-2">
                <Checkbox
                    :inputId="`required-${props.index}`"
                    :value="props.element.required"
                    :checked="!!props.element.required"
                    @change="(event: Event) => handleCheckboxChange('required', event)"
                />
                <label :for="`required-${props.index}`"> Required field </label>
            </div>
            <div class="flex items-center gap-2">
                <Checkbox
                    :inputId="`has_action-${props.index}`"
                    :value="props.element.has_action"
                    :checked="!!props.element.has_action"
                    @change="(event: Event) => handleCheckboxChange('has_action', event)"
                />
                <label :for="`has_action-${props.index}`"> Has Action </label>
            </div>
            <div
                v-for="attr in ['label', 'type', 'placeholder', 'options']"
                :key="props.index + '-' + attr">
                <Message v-show="props.errors?.['elements.' + props.index + '.' + attr]" severity="error" variant="simple" size="small">{{ props.errors?.['elements.' + props.index + '.' + attr] }}</Message>
            </div>
        </div>
    </div>
</template>

