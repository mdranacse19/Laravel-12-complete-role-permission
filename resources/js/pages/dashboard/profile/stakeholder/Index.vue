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
import { index as stakeholdersIndex, destroy as stakeholdersDestroy } from '@/routes/profile/stakeholders';
import StakeholderForm from './components/StakeholderForm.vue';
import { formatDate } from '@/utils/helpers';

const DEBOUNCE_DELAY = 1500;
const DEFAULT_PAGE_SIZE = 10;
const PAGE_SIZE_OPTIONS = [10, 15, 20, 25, 50];

interface Stakeholder {
    id: number;
    typeName: string;
    typeTag: string;
    name: string;
    bnName?: string;
    designation?: string;
    bnDesignation?: string;
    mobileNo?: string | any;
    email?: string;
    password?: string;
    isActive: boolean;
}

interface PaginatedStakeholder {
    data: Stakeholder[];
    total: number;
    per_page: number;
    current_page: number;
}

interface TypeData {
    label: string;
    value: string;
}

const props = defineProps<{
    Stakeholders: PaginatedStakeholder;
    types?: TypeData[];

}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Setup', href: '#' },
    { title: 'Stakeholders', href: stakeholdersIndex().url },
];

const StakeholdersList = ref<Stakeholder[]>(props.Stakeholders?.data ?? []);
const totalStakeholders = ref(props.Stakeholders?.total ?? 0);
const perPage = ref(props.Stakeholders?.per_page ?? DEFAULT_PAGE_SIZE);
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
const editingStakeholder = ref<Stakeholder | null>(null);
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

    router.get(stakeholdersIndex().url, {
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
            const StakeholdersData = page.props.Stakeholders as PaginatedStakeholder;
            StakeholdersList.value = StakeholdersData?.data ?? [];
            totalStakeholders.value = StakeholdersData?.total ?? 0;
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
const transformToFormData = (stakeholder: Stakeholder) => ({
    id: stakeholder.id,
    type: stakeholder.typeName,
    name: stakeholder.name,
    bnName: stakeholder.bnName,
    designation: stakeholder.designation,
    bnDesignation: stakeholder.bnDesignation,
    mobileNo: stakeholder.mobileNo,
    email: stakeholder.email,
    password: '',
    isActive: stakeholder.isActive,
});

const openCreateModal = () => {
    editingStakeholder.value = null;
    formMode.value = 'create';
    showFormModal.value = true;
};

const openEditModal = (stakeholder: Stakeholder) => {
    editingStakeholder.value = transformToFormData(stakeholder) as any;
    formMode.value = 'edit';
    showFormModal.value = true;
};

const closeModal = () => {
    showFormModal.value = false;
    editingStakeholder.value = null;
};

const handleFormSuccess = () => {
    closeModal();
    loadAssociations({ rows: perPage.value, page: currentPage.value });
};

const deleteAssociation = (associationID: number, name: string) => {
    confirm.require({
        header: 'Delete Stakeholder',
        message: `Are you sure you want to delete "${name}"?`,
        accept: () => {
            router.delete(stakeholdersDestroy(associationID).url, {
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
    <Head title="Stakeholders" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageHeader>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Stakeholders
            </h2>

            <Button v-if="hasPermission('stakeholder_create')" label="Add Stakeholder" icon="pi pi-plus" size="small" @click="openCreateModal" />
        </template>

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="relative min-h-screen flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <DataTable
                    :value="isLoading ? Array(perPage).fill({}) : StakeholdersList"
                    @page="loadAssociations($event)"
                    @sort="onSort($event)"
                    :first="currentPage * perPage"
                    lazy
                    :paginator="totalStakeholders > perPage"
                    removableSort
                    stripedRows
                    :totalRecords="totalStakeholders"
                    :rows="perPage"
                    :rowsPerPageOptions="PAGE_SIZE_OPTIONS"
                    tableStyle="min-width: 50rem"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} Stakeholders">
                    <template #empty> No Items found. </template>
                    <template #header>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-gray-500 dark:text-surface-400 font-medium">
                                Showing {{ StakeholdersList.length }} of {{ totalStakeholders }} Stakeholders
                            </span>

                            <IconField iconPosition="left">
                                <InputIcon>
                                    <i class="pi pi-search"></i>
                                </InputIcon>
                                <InputText v-model="search" placeholder="Search" />
                            </IconField>
                        </div>
                    </template>

                    <Column field="isActive" header="Status" style="width: 100px; height: 44px">
                        <template #body="slotProps">
                            <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                <Skeleton width="60%" height="1rem" />
                            </div>
                            <Tag v-else :severity="slotProps.data.typeTag">
                                {{ slotProps.data.typeName }}
                            </Tag>
                        </template>
                    </Column>

                    <Column field="name" header="Name" sortable style="height: 44px">
                        <template #body="slotProps">
                            <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                <Skeleton width="60%" height="1rem" />
                            </div>
                            <span v-else>{{ slotProps.data.name }}</span>
                        </template>
                    </Column>

                    <Column field="designation" header="Designation" style="height: 44px">
                        <template #body="slotProps">
                            <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                <Skeleton width="80%" height="1rem" />
                            </div>
                            <span v-else>{{ slotProps.data.designation || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="mobileNo" header="Mobile" style="height: 44px">
                        <template #body="slotProps">
                            <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                <Skeleton width="80%" height="1rem" />
                            </div>
                            <span v-else>{{ slotProps.data.mobileNo || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="email" header="Email" style="height: 44px">
                        <template #body="slotProps">
                            <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                <Skeleton width="80%" height="1rem" />
                            </div>
                            <span v-else>{{ slotProps.data.email || '-' }}</span>
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
                        v-if="hasPermission(['stakeholder_update', 'stakeholder_delete'])"
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
                                    v-if="hasPermission('stakeholder_update')"
                                    icon="pi pi-pencil"
                                    severity="info"
                                    size="small"
                                    text
                                    rounded
                                    v-tooltip.top="'Edit Stakeholder'"
                                    @click="openEditModal(slotProps.data)"
                                />
                                <Button
                                    v-if="hasPermission('stakeholder_delete')"
                                    @click="deleteAssociation(slotProps.data.id, slotProps.data.name)"
                                    icon="pi pi-trash"
                                    severity="danger"
                                    size="small"
                                    text
                                    rounded
                                    v-tooltip.top="'Delete Stakeholder'"
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
            :header="formMode === 'create' ? 'Create Stakeholder' : 'Edit Stakeholder'"
            :style="{ width: '50rem' }"
        >
            <StakeholderForm
                :mode="formMode"
                :stakeholder="editingStakeholder"
                :types="types"
                @success="handleFormSuccess"
                @cancel="closeModal"
            />
        </Dialog>
    </AppLayout>
</template>
