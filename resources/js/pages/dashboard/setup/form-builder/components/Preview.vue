<script setup lang="ts">
import RadioButton from 'primevue/radiobutton';
import RadioButtonGroup from 'primevue/radiobuttongroup';
// import InputLabel from "@/Components/InputLabel.vue";
import { computed, type Component } from "vue";
import InputText from "primevue/inputtext";
import Calendar from 'primevue/datepicker';
import Dropdown from "primevue/dropdown";
import Textarea from "primevue/textarea";
import Checkbox from 'primevue/checkbox';
import MultiSelect from "primevue/multiselect";

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
    index: number;
}

interface SelectOption {
    value: string;
    label: string;
}

interface InputAttributes {
    placeholder?: string;
    required?: boolean;
    class?: string;
    type?: string;
    rows?: number;
    optionLabel?: string;
    optionValue?: string;
    options?: SelectOption[];
}

interface ComponentMap {
    [key: string]: Component;
}

const props = defineProps<Props>();

// Computed Properties
const inputProps = computed<InputAttributes>(() => {
    const type = props.element.type;
    const attrs: InputAttributes = {
        placeholder: props.element.placeholder,
        required: props.element.required,
        class: "w-full",
    };

    if (type === 'select' || type === 'multiSelect' || type === 'radio' || type === 'checkbox') {
        return {
            ...attrs,
            optionLabel: 'label',
            optionValue: 'value',
            options: props.element.options ? props.element.options.split('\n')
                .filter((line: string) => String(line).trimStart())
                .map((opt: string): SelectOption => {
                    return {
                        value: opt,
                        label: opt,
                    };
                }) : [],
        };
    }

    if (type === 'date') {
        // Remove 'type' property for date inputs
        const { type: _type, ...dateAttrs } = attrs;
        return dateAttrs;
    }

    if (type === 'textarea') {
        return {
            ...attrs,
            rows: 4,
        };
    }

    return {
        ...attrs,
        type,
    };
});

const componentMap: ComponentMap = {
    text: InputText,
    email: InputText,
    number: InputText,
    date: Calendar,
    select: Dropdown,
    multiSelect: MultiSelect,
    textarea: Textarea,
    checkbox: Checkbox,
    radio: RadioButton,
};

const inputComponent = computed<Component>(() => {
    return componentMap[props.element.type] || InputText;
});
</script>
<template>
    <div class="mb-4">
        <label :for="props.element.id">{{ props.element.label }} <span :v-if="!!props.element.required" class="text-red">*</span></label>
        <div v-if="props.element.type === 'radio'">
            <RadioButtonGroup name="ingredient" class="flex flex-wrap gap-4">
                <div v-for="(radio, index) in inputProps.options" :key="`radio-key-${index}`" class="flex items-center gap-2">
                    <RadioButton :inputId="`radio-id-${index}`" :name="`radio-${props.element.label.replace(/ /g, '_').toLowerCase().trim()}`" :value="`radio-${index}`"/>
                    <label :for="`radio-id-${index}`" class="ml-2">{{ radio.label }}</label>
                </div>
            </RadioButtonGroup>
        </div>
        <div v-else-if="props.element.type === 'checkbox'" class="flex items-center gap-2 my-3">
            <div v-for="(field, key) in inputProps.options" :key="key" class="flex items-center gap-2">
                <Checkbox :inputId="`${field.value}${key}`" :name="`radio-${props.element.label.replace(/ /g, '_').toLowerCase().trim()}-${key}`" :value="true" checked/>
                <label :for="`${field.value}${key}`">{{ field.label }}</label>
            </div>
        </div>
        <div v-else>
            <component
                :is="inputComponent"
                v-bind="inputProps"
            />
        </div>
    </div>
</template>
