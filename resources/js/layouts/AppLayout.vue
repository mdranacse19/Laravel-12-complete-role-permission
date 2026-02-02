<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import Preloader from '@/components/Preloader.vue';
import RouteLoader from '@/components/RouteLoader.vue';
import type { BreadcrumbItemType } from '@/types';
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <!-- Global Preloader -->
    <Preloader />

    <!-- Route Navigation Loader -->
    <RouteLoader />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section v-if="$slots.pageHeader">
            <div
                class="w-full flex justify-between mx-auto py-6 px-4 sm:px-6 lg:px-8"
            >
                <slot name="pageHeader" />
            </div>
        </section>

        <slot />

    </AppLayout>

    <!-- PrimeVue Toast Components -->
    <Toast />
    <Toast position="top-left" group="tl" />
    <Toast position="top-right" group="tr" />
    <Toast position="bottom-left" group="bl" />
    <Toast position="bottom-right" group="br" />

    <!-- PrimeVue Confirm Dialog Component -->
    <ConfirmDialog></ConfirmDialog>
</template>
