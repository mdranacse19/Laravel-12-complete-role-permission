<script setup lang="ts">

</script>
<template>

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl px-4 md:px-6">
        <div class="relative min-h-[100vh] flex-1 rounded-xl  border-sidebar-border/70 md:min-h-min">
            <div class="bg-white/20 shadow border sm:rounded-lg p-4">
                <slot />
            </div>
        </div>
    </div>
</template>
<style scoped>
:deep(.p-datatable-header) {
    padding-top: 0 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}
</style>
