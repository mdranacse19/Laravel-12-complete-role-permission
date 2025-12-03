<script setup lang="ts">
import { computed } from 'vue';
import DatePicker from 'primevue/datepicker';
import moment from 'moment';

type SelectionMode = 'single' | 'multiple' | 'range';

interface Props {
    modelValue?: string | string[] | null;
    selectionMode?: SelectionMode;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: null,
    selectionMode: 'single',
});

const emit = defineEmits<{
    'update:modelValue': [value: string | string[] | null];
}>();

const internalDate = computed<Date | Date[] | (Date | null)[] | null>({
    get(): Date | Date[] | (Date | null)[] | null {
        if (!props.modelValue) return null;
        
        if (props.selectionMode === 'single') {
            const date = momentDate(props.modelValue as string);
            return date.isValid() ? date.toDate() : null;
        } 
        else if (props.selectionMode === 'multiple') {
            return Array.isArray(props.modelValue)
                ? props.modelValue
                    .map((dateStr: string) => {
                        const date = momentDate(dateStr);
                        return date.isValid() ? date.toDate() : null;
                    })
                    .filter((date): date is Date => date !== null)
                : [];
        }
        else { // range
            return Array.isArray(props.modelValue) && props.modelValue.length === 2
                ? props.modelValue.map((dateStr: string) => {
                    const date = momentDate(dateStr);
                    return date.isValid() ? date.toDate() : null;
                })
                : [null, null];
        }
    },
    set(value: Date | Date[] | (Date | null)[] | null) {
        if (!value) {
            emit('update:modelValue', props.selectionMode === 'single' ? null : []);
            return;
        }

        const formatOrNull = (date: Date | null): string | null => {
            if (!date) return null;
            const m = moment(date);
            return m.isValid() ? m.format('YYYY-MM-DD') : null;
        };

        if (props.selectionMode === 'single') {
            emit('update:modelValue', formatOrNull(value as Date));
        }
        else if (props.selectionMode === 'multiple') {
            const dates = Array.isArray(value)
                ? value.map(formatOrNull).filter((d): d is string => d !== null)
                : [];
            emit('update:modelValue', dates);
        }
        else { // range
            const dates = Array.isArray(value) && value.length === 2
                ? value.map(formatOrNull).filter((d): d is string => d !== null)
                : [];
            emit('update:modelValue', dates.length === 2 ? dates : []);
        }
    },
});

// Helper to validate date strings
function momentDate(dateString: string): moment.Moment {
    return moment(dateString, 'YYYY-MM-DD', true);
}
</script>

<template>
    <DatePicker 
        v-model="internalDate" 
        dateFormat="yy-mm-dd"
        :selectionMode="selectionMode"
    />
</template>