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
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { refDebounced } from '@vueuse/core';
import { showAxiosErrors } from "@/Helpers/toast";
import { hasPermission } from '@/Helpers/authorization';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { index as rolesIndex, show as rolesShow, destroy as rolesDestroy, create as rolesCreate, edit as rolesEdit } from '@/routes/roles';

interface Role {
    id: number;
    name: string;
    deletable: boolean;
}

interface PaginatedRoles {
    data: Role[];
    total: number;
    per_page: number;
    current_page: number;
}

const props = defineProps<{
    roles: PaginatedRoles;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Roles',
        href: rolesIndex().url,
    },
];

const rolesList = ref<Role[]>(props.roles?.data ?? []);
const totalRoles = ref(props.roles?.total ?? 0);
const perPage = ref(props.roles?.per_page ?? 10);
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

    router.get(rolesIndex().url, {
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
            const rolesData = page.props.roles as PaginatedRoles;
            rolesList.value = rolesData?.data ?? [];
            totalRoles.value = rolesData?.total ?? 0;
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

const deleteRole = (roleID: number) => {
    confirm.require({
        header: 'Delete Role',
        message: 'Are you sure you want to delete this role?',
        accept: () => {
            router.delete(rolesDestroy(roleID).url, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (page: any) => {
                    const rolesData = page.props.roles as PaginatedRoles;
                    rolesList.value = rolesData?.data ?? [];
                    totalRoles.value = rolesData?.total ?? 0;

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
    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageHeader>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Roles
            </h2>

            <Button as="a" v-if="hasPermission('role_create')" label="Add New Role" icon="pi pi-plus" size="small" :href="rolesCreate().url" />
        </template>

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4">
                    <DataTable
                        :value="isLoading ? Array(perPage).fill({}) : rolesList"
                        @page="loadRoles($event)"
                        @sort="onSort($event)"
                        :first="currentPage * perPage"
                        lazy
                        :paginator="totalRoles > perPage"
                        removableSort
                        stripedRows
                        :totalRecords="totalRoles"
                        :rows="perPage"
                        :rowsPerPageOptions="[10, 15, 20, 25, 50]"
                        tableStyle="min-width: 50rem"
                        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} roles">
                        <template #empty> No Items found. </template>
                        <template #header>
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="text-gray-500 dark:text-surface-400 font-medium">
                                    Showing {{ rolesList.length }} of {{ totalRoles }} roles
                                </span>

                                <IconField iconPosition="left">
                                    <InputIcon>
                                        <i class="pi pi-search"></i>
                                    </InputIcon>
                                    <InputText v-model="search" placeholder="Search" />
                                </IconField>
                            </div>
                        </template>

                        <Column field="name" header="Role Name" sortable style="height: 44px">
                            <template #body="slotProps">
                                <div v-if="isLoading" class="flex items-center" :style="{ height: '17px', 'flex-grow': '1', overflow: 'hidden' }">
                                    <Skeleton width="60%" height="1rem" />
                                </div>
                                <span v-else>{{ slotProps.data.name }}</span>
                            </template>
                        </Column>
                        <Column
                            v-if="hasPermission(['role_update', 'role_delete'])"
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
                                        v-if="hasPermission('role_update')"
                                        as="a"
                                        icon="pi pi-pencil"
                                        severity="info"
                                        size="small"
                                        text
                                        rounded
                                        v-tooltip.top="'Edit Role'"
                                        :href="rolesEdit(slotProps.data.id).url"
                                    />
                                    <Button
                                        v-if="hasPermission('role_delete') && slotProps.data.deletable"
                                        @click="deleteRole(slotProps.data.id)"
                                        icon="pi pi-trash"
                                        severity="danger"
                                        size="small"
                                        text
                                        rounded
                                        v-tooltip.top="'Delete Role'"
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
