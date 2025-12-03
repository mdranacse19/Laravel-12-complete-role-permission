<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Skeleton from 'primevue/skeleton';
import Tag from 'primevue/tag';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { refDebounced } from '@vueuse/core';
import { showAxiosErrors } from "@/Helpers/toast";
import { hasPermission } from '@/Helpers/authorization';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { index as formsIndex, show as formsShow, destroy as formsDestroy, create as formsCreate, edit as formsEdit } from '@/routes/setup/dynamic-form';

interface Form {
    id: number;
    name: string;
    deletable: boolean;
}

interface PaginatedForms {
    data: Form[];
    total: number;
    per_page: number;
    current_page: number;
}

const props = defineProps<{
    forms: PaginatedForms;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Forms',
        href: formsIndex().url,
    },
];

const formsList = ref<Form[]>(props.forms?.data ?? []);
const totalForms = ref(props.forms?.total ?? 0);
const perPage = ref(props.forms?.per_page ?? 10);
const currentPage = ref(0);
const isLoading = ref(false);
const orderBy = ref('');
const orderDirection = ref('desc');
const search = ref('');
const debounced = refDebounced(search, 1500);
const confirm = useConfirm();
const toast = useToast();
const page = usePage();

watch(debounced, () => {
    currentPage.value = 0;
    loadRoles({
        rows: perPage.value,
        page: 0,
    });
});

const loadRoles = (event: { rows: number; page: number }) => {
    perPage.value = event.rows;
    currentPage.value = event.page;

    router.get(formsIndex().url, {
        search: search.value,
        per_page: perPage.value,
        page: currentPage.value + 1,
        order_by: orderBy.value,
        order_direction: orderDirection.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        onBefore: () => {
            isLoading.value = true;
        },
        onSuccess: (page: any) => {
            const rolesData = page.props.forms as PaginatedForms;
            formsList.value = rolesData?.data ?? [];
            totalForms.value = rolesData?.total ?? 0;
        },
        onError: (errors: any) => {
            showAxiosErrors(toast, errors);
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

const onSort = (event: any) => {
    orderBy.value = event.sortField ?? '';
    orderDirection.value = event.sortOrder === 1 ? 'asc' : 'desc';

    loadRoles({
        rows: perPage.value,
        page: currentPage.value,
    });
};

const deleteDynamicForm = (formID: number) => {
    confirm.require({
        header: 'Delete Dynamic Form',
        message: 'Are you sure you want to delete this dynamic form?',
        accept: () => {
            router.delete(formsDestroy(formID).url, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (page: any) => {
                    const rolesData = page.props.forms as PaginatedForms;
                    formsList.value = rolesData?.data ?? [];
                    totalForms.value = rolesData?.total ?? 0;

                    // Show success message from flash
                    if (page.props.flash?.success) {
                        toast.add({
                            severity: 'success',
                            summary: 'Success',
                            detail: page.props.flash.success,
                            group: 'br',
                            life: 3000
                        });
                    }
                },
                onError: (errors: any) => {
                    showAxiosErrors(toast, errors);
                },
            });
        },
        reject: () => {
            toast.add({
                severity: 'info',
                summary: 'Cancelled',
                detail: 'No action was taken.',
                group: 'br',
                life: 3000
            });
        }
    });
};
</script>

<template>
    <Head title="Forms" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageHeader>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Form Builder
            </h2>

            <Button as="a" v-if="hasPermission('form_builder_create')" label="Add New Form" icon="pi pi-plus" size="small" :href="formsCreate().url" />
        </template>

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4">
                    <DataTable
                        :value="isLoading ? Array(perPage).fill({}) : formsList"
                        @page="loadRoles($event)"
                        @sort="onSort($event)"
                        :first="currentPage * perPage"
                        lazy
                        :paginator="totalForms > perPage"
                        removableSort
                        stripedRows
                        :totalRecords="totalForms"
                        :rows="perPage"
                        :rowsPerPageOptions="[10, 15, 20, 25, 50]"
                        tableStyle="min-width: 50rem"
                        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} roles">
                        <template #empty> No Items found. </template>
                        <template #header>
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="text-gray-500 dark:text-surface-400 font-medium">
                                    Showing {{ formsList.length }} of {{ totalForms }} roles
                                </span>

                                <IconField iconPosition="left">
                                    <InputIcon>
                                        <i class="pi pi-search"></i>
                                    </InputIcon>
                                    <InputText v-model="search" placeholder="Search" />
                                </IconField>
                            </div>
                        </template>

                        <Column field="name" header="Form Name" sortable style="height: 44px">
                            <template #body="slotProps">
                                <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                    <Skeleton width="60%" height="1rem" />
                                </div>
                                <span v-else>{{ slotProps.data.name }}</span>
                            </template>
                        </Column>

                        <Column field="type_name" header="Form Type" sortable style="height: 44px">
                            <template #body="slotProps">
                                <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                    <Skeleton width="60%" height="1rem" />
                                </div>
                                <!-- <span v-else>{{ slotProps.data.name }}</span> -->
                                <Tag v-else :severity="slotProps.data.type_tag">
                                    {{ slotProps.data.type_name }}
                                </Tag>
                            </template>
                        </Column>

                        <Column field="is_active" header="Status" style="width: 100px; height: 44px">
                            <template #body="slotProps">
                                <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                    <Skeleton width="60%" height="1rem" />
                                </div>
                                <Tag v-else :severity="slotProps.data.is_active ? 'success' : 'danger'">
                                    {{ slotProps.data.is_active ? 'Active' : 'Inactive' }}
                                </Tag>
                            </template>
                        </Column>
                        <Column
                            v-if="hasPermission(['form_builder_update', 'form_builder_delete'])"
                            header="Actions"
                            style="width: 150px; height: 44px"
                        >
                            <template #body="slotProps">
                                <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                    <Skeleton width="40%" height="1rem" />
                                    <Skeleton width="40%" height="1rem" />
                                </div>
                                <div v-else class="flex gap-2">
                                    <Button
                                        v-if="hasPermission('form_builder_update')"
                                        as="a"
                                        icon="pi pi-pencil"
                                        severity="info"
                                        size="small"
                                        text
                                        rounded
                                        v-tooltip.top="'Edit Form'"
                                        :href="formsEdit(slotProps.data.id).url"
                                    />
                                    <Button
                                        v-if="hasPermission('form_builder_delete')"
                                        @click="deleteDynamicForm(slotProps.data.id)"
                                        icon="pi pi-trash"
                                        severity="danger"
                                        size="small"
                                        text
                                        rounded
                                        v-tooltip.top="'Delete Form'"
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
