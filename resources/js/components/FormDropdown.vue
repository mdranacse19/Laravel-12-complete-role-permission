<template>
  <div>
    <div v-if="isProcessing" class="mb-2">
      <Skeleton width="30%" height="1rem" class="mb-2" />
      <Skeleton width="100%" height="2.5rem" />
    </div>

    <div v-else>
      <label :for="id" class="mb-2 block text-sm font-medium text-gray-800 dark:text-gray-300">
        {{ label }} <span v-if="required" class="text-red-500">*</span>
      </label>

      <Dropdown
        :id="id"
        v-model="modelValueProxy"
        :options="options"
        :optionLabel="optionLabel"
        :optionValue="optionValue"
        :placeholder="placeholder"
        class="w-full"
        :disabled="isProcessing"
      />

      <Message v-if="error" severity="error" variant="simple" size="small" class="mt-2">
        {{ error }}
      </Message>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Dropdown from 'primevue/dropdown';
import Skeleton from 'primevue/skeleton';
import Message from 'primevue/message';

const props = defineProps({
  id: { type: String, required: true },
  modelValue: { required: true },
  label: { type: String, default: '' },
  required: { type: Boolean, default: false },
  isProcessing: { type: Boolean, default: false },
  error: { type: [String, Boolean, Object], default: '' },
  options: { type: Array, default: () => [] },
  optionLabel: { type: String, default: 'label' },
  optionValue: { type: String, default: 'value' },
  placeholder: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

// Two-way binding proxy to support v-model on the component
const modelValueProxy = computed({
  get: () => props.modelValue,
  set: (val: any) => emit('update:modelValue', val),
});
</script>

