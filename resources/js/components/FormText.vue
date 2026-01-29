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

      <InputText
        :id="id"
        v-model="modelValueProxy"
        :placeholder="placeholder"
        :disabled="isProcessing"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"
      />

      <Message v-if="error" severity="error" variant="simple" size="small" class="mt-2">
        {{ error }}
      </Message>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Skeleton from 'primevue/skeleton';
import Message from 'primevue/message';
import InputText from 'primevue/inputtext';

const props = defineProps({
  id: { type: String, required: true },
  modelValue: { required: true },
  label: { type: String, default: '' },
  required: { type: Boolean, default: false },
  isProcessing: { type: Boolean, default: false },
  error: { type: [String, Boolean, Object], default: '' },
  placeholder: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const modelValueProxy = computed<any>({
  get: () => props.modelValue as any,
  set: (val: any) => emit('update:modelValue', val),
});
</script>
