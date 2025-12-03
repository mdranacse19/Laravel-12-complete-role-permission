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
import Dialog from 'primevue/dialog';
import Tag from 'primevue/tag';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { refDebounced } from '@vueuse/core';
import { showAxiosErrors } from "@/Helpers/toast";
import { hasPermission } from '@/Helpers/authorization';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { index as associationTypesIndex, destroy as associationTypesDestroy } from '@/routes/setup/association-type';
import AssociationTypeForm from './components/AssociationTypeForm.vue';
import { formatDate } from '@/utils/helpers';

const DEBOUNCE_DELAY = 1500;
const DEFAULT_PAGE_SIZE = 10;
const PAGE_SIZE_OPTIONS = [10, 15, 20, 25, 50];

interface AssociationType {
    id: number;
    name: string;
    description?: string;
    appKey?: string | any;
    validUntil?: string;
    token?: string;
    isActive: boolean;
    deletable: boolean;
}

interface PaginatedAssociationType {
    data: AssociationType[];
    total: number;
    per_page: number;
    current_page: number;
}

const props = defineProps<{
    associationTypes: PaginatedAssociationType;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Setup', href: '#' },
    { title: 'Association Types', href: associationTypesIndex().url },
];

const associationTypesList = ref<AssociationType[]>(props.associationTypes?.data ?? []);
const totalAssociationTypes = ref(props.associationTypes?.total ?? 0);
const perPage = ref(props.associationTypes?.per_page ?? DEFAULT_PAGE_SIZE);
const currentPage = ref(0);
const isLoading = ref(false);
const orderBy = ref('');
const orderDirection = ref<'asc' | 'desc'>('desc');
const search = ref('');
const debounced = refDebounced(search, DEBOUNCE_DELAY);
const confirm = useConfirm();
const toast = useToast();
const page = usePage();

const showFormModal = ref(false);
const editingAssociation = ref<AssociationType | null>(null);
const formMode = ref<'create' | 'edit'>('create');

// Watch for flash messages
watch(
    () => page.props.flash,
    (flash: any) => {
        if (flash?.success) {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: flash.success,
                group: 'br',
                life: 3000
            });
        }
    },
    { immediate: true, deep: true }
);

// Watch search and reset to first page
watch(debounced, () => {
    currentPage.value = 0;
    loadAssociations({ rows: perPage.value, page: 0 });
});

const loadAssociations = (event: { rows: number; page: number }) => {
    perPage.value = event.rows;
    currentPage.value = event.page;

    router.get(associationTypesIndex().url, {
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
            const associationTypesData = page.props.associationTypes as PaginatedAssociationType;
            associationTypesList.value = associationTypesData?.data ?? [];
            totalAssociationTypes.value = associationTypesData?.total ?? 0;
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
    loadAssociations({ rows: perPage.value, page: currentPage.value });
};

// Transform snake_case backend data to camelCase for form
const transformToFormData = (association: AssociationType) => ({
    id: association.id,
    name: association.name,
    description: association.description,
    appKey: association.appKey,
    validUntil: association.validUntil,
    token: association.token,
    isActive: association.isActive,
});

const openCreateModal = () => {
    editingAssociation.value = null;
    formMode.value = 'create';
    showFormModal.value = true;
};

const openEditModal = (association: AssociationType) => {
    editingAssociation.value = transformToFormData(association) as any;
    formMode.value = 'edit';
    showFormModal.value = true;
};

const closeModal = () => {
    showFormModal.value = false;
    editingAssociation.value = null;
};

const handleFormSuccess = () => {
    closeModal();
    loadAssociations({ rows: perPage.value, page: currentPage.value });
};

const deleteAssociation = (associationID: number, name: string) => {
    confirm.require({
        header: 'Delete Association Type',
        message: `Are you sure you want to delete "${name}"?`,
        accept: () => {
            router.delete(associationTypesDestroy(associationID).url, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => loadAssociations({ rows: perPage.value, page: currentPage.value }),
                onError: (errors: any) => showAxiosErrors(toast, errors),
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
    <Head title="Association Types" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageHeader>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Association Types
            </h2>

            <Button v-if="hasPermission('association_type_create')" label="Add Association Type" icon="pi pi-plus" size="small" @click="openCreateModal" />
        </template>

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="relative min-h-screen flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <DataTable
                    :value="isLoading ? Array(perPage).fill({}) : associationTypesList"
                    @page="loadAssociations($event)"
                    @sort="onSort($event)"
                    :first="currentPage * perPage"
                    lazy
                    :paginator="totalAssociationTypes > perPage"
                    removableSort
                    stripedRows
                    :totalRecords="totalAssociationTypes"
                    :rows="perPage"
                    :rowsPerPageOptions="PAGE_SIZE_OPTIONS"
                    tableStyle="min-width: 50rem"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} association types">
                    <template #empty> No Items found. </template>
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-gray-500 dark:text-surface-400 font-medium">
                                Showing {{ associationTypesList.length }} of {{ totalAssociationTypes }} association types
                            </span>

                            <IconField iconPosition="left">
                                <InputIcon>
                                    <i class="pi pi-search"></i>
                                </InputIcon>
                                <InputText v-model="search" placeholder="Search" />
                            </IconField>
                        </div>
                    </template>

                    <Column field="name" header="Name" sortable style="height: 44px">
                        <template #body="slotProps">
                            <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                <Skeleton width="60%" height="1rem" />
                            </div>
                            <span v-else>{{ slotProps.data.name }}</span>
                        </template>
                    </Column>

                    <Column field="description" header="Description" style="height: 44px">
                        <template #body="slotProps">
                            <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                <Skeleton width="80%" height="1rem" />
                            </div>
                            <span v-else>{{ slotProps.data.description || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="validUntil" header="Valid Until" style="width: 150px; height: 44px">
                        <template #body="slotProps">
                            <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                <Skeleton width="70%" height="1rem" />
                            </div>
                            <span v-else>{{ formatDate(slotProps.data.validUntil, "MMM D, YYYY") || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="isActive" header="Status" style="width: 100px; height: 44px">
                        <template #body="slotProps">
                            <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                <Skeleton width="60%" height="1rem" />
                            </div>
                            <Tag v-else :severity="slotProps.data.isActive ? 'success' : 'danger'">
                                {{ slotProps.data.isActive ? 'Active' : 'Inactive' }}
                            </Tag>
                        </template>
                    </Column>

                    <Column
                        v-if="hasPermission(['association_type_update', 'association_type_delete'])"
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
                                    v-if="hasPermission('association_type_update')"
                                    icon="pi pi-pencil"
                                    severity="info"
                                    size="small"
                                    text
                                    rounded
                                    v-tooltip.top="'Edit Association Type'"
                                    @click="openEditModal(slotProps.data)"
                                />
                                <Button
                                    v-if="hasPermission('association_type_delete')"
                                    @click="deleteAssociation(slotProps.data.id, slotProps.data.name)"
                                    icon="pi pi-trash"
                                    severity="danger"
                                    size="small"
                                    text
                                    rounded
                                    v-tooltip.top="'Delete Association Type'"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <!-- Modal Form -->
        <Dialog
            v-model:visible="showFormModal"
            modal
            :header="formMode === 'create' ? 'Create Association Type' : 'Edit Association Type'"
            :style="{ width: '30rem' }"
        >
            <AssociationTypeForm
                :mode="formMode"
                :association="editingAssociation"
                @success="handleFormSuccess"
                @cancel="closeModal"
            />
        </Dialog>
    </AppLayout>
</template>
