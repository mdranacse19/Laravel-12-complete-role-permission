<script setup lang="ts">
import { hasPermission } from '@/Helpers/authorization';
import { showAxiosErrors } from '@/Helpers/toast';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { destroy as userDestroy, index as userIndex } from '@/routes/users';
import { type BreadcrumbItem } from '@/types';
import { type User } from '@/types';

interface Role {
    id: number;
    name: string;
}

const DEBOUNCE_DELAY = 1500;
const DEFAULT_PAGE_SIZE = 10;
const PAGE_SIZE_OPTIONS = [10, 15, 20, 25, 50];

import { Head, router, usePage } from '@inertiajs/vue3';
import { refDebounced } from '@vueuse/core';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Skeleton from 'primevue/skeleton';
import Tag from 'primevue/tag';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { ref, watch } from 'vue';
import UserForm from './UserForm.vue';
import UserPasswordForm from './UserPasswordForm.vue';
import Card from '@/components/dashboard/Card.vue';

interface PaginatedUsers {
    data: User[];
    total: number;
    per_page: number;
    current_page: number;
}

const props = defineProps<{
    roles: Role[];
    users: PaginatedUsers;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Users', href: userIndex().url },
];

const userList = ref<User[]>(props.users?.data ?? []);
const totalUsers = ref(props.users?.total ?? 0);
const perPage = ref(props.users?.per_page ?? DEFAULT_PAGE_SIZE);
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
const user = ref<User | null>(null);
const formMode = ref<'create' | 'edit'>('create');

const showPasswordModal = ref(false);
const passwordUser = ref<User | null>(null);

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
                life: 3000,
            });
        }
    },
    { immediate: true, deep: true },
);

// Watch search and reset to the first page
watch(debounced, () => {
    currentPage.value = 0;
    loadLists({ rows: perPage.value, page: 0 });
});

const loadLists = (event: { rows: number; page: number }) => {
    perPage.value = event.rows;
    currentPage.value = event.page;

    router.get(
        userIndex().url,
        {
            search: search.value,
            per_page: perPage.value,
            page: currentPage.value + 1,
            order_by: orderBy.value,
            order_direction: orderDirection.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onBefore: () => (isLoading.value = true),
            onSuccess: (page: any) => {
                const userData = page.props.users;
                userList.value = userData?.data ?? [];
                totalUsers.value = userData?.total ?? 0;
            },
            onError: (errors: any) => showAxiosErrors(toast, errors),
            onFinish: () => (isLoading.value = false),
        },
    );
};

const onSort = (event: any) => {
    orderBy.value = event.sortField ?? '';
    orderDirection.value = event.sortOrder === 1 ? 'asc' : 'desc';
    loadLists({ rows: perPage.value, page: currentPage.value });
};

// Transform snake_case backend data to camelCase for form
const transformToFormData = (user: any) => ({
    id: user.id,
    fullName: user.name,
    email: user.email,
    role: user.roles[0]?.id,
    bengaliName: user.bn_name,
    designation: user.designation,
    bnDesignation: user.bn_designation,
    isActive: user.is_active,
});

const openCreateModal = () => {
    user.value = null;
    formMode.value = 'create';
    showFormModal.value = true;
};

const openEditModal = (u: User) => {
    user.value = transformToFormData(u) as any;
    formMode.value = 'edit';
    showFormModal.value = true;
};

const openPasswordModal = (u: User) => {
    passwordUser.value = u as any;
    showPasswordModal.value = true;
};

const closeModal = () => {
    showFormModal.value = false;
    user.value = null;
};

const closePasswordModal = () => {
    showPasswordModal.value = false;
    passwordUser.value = null;
};

const handleFormSuccess = () => {
    closeModal();
};

const deleteUser = (userID: number, name: string) => {
    confirm.require({
        header: 'Delete User',
        message: `Are you sure you want to delete "${name}"?`,
        accept: () => {
            router.delete(userDestroy(userID).url, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () =>
                    loadLists({ rows: perPage.value, page: currentPage.value }),
                onError: (errors: any) => showAxiosErrors(toast, errors),
            });
        },
        reject: () => {
            toast.add({
                severity: 'info',
                summary: 'Cancelled',
                detail: 'No action was taken.',
                group: 'br',
                life: 3000,
            });
        },
    });
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageHeader>
            <h2
                class="text-xl leading-tight font-semibold text-gray-800 dark:text-gray-200"
            >
                Users
            </h2>

            <Button
                v-if="hasPermission('material_category_create')"
                label="Add User"
                icon="pi pi-plus"
                size="small"
                @click="openCreateModal"
            />
        </template>

        <Card>
            <DataTable
                :value="isLoading ? Array(perPage).fill({}) : userList"
                @page="loadLists($event)"
                @sort="onSort($event)"
                :first="currentPage * perPage"
                lazy
                :paginator="totalUsers > perPage"
                removableSort
                stripedRows
                :totalRecords="totalUsers"
                :rows="perPage"
                :rowsPerPageOptions="PAGE_SIZE_OPTIONS"
                tableStyle="min-width: 50rem"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} Users"
            >
                <template #empty> No Items found.</template>
                <template #header>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <span class="dark:text-surface-400 font-medium text-gray-500">
                                Showing {{ userList.length }} of {{ totalUsers }} Users
                            </span>
                        </div>
                        <IconField iconPosition="left">
                            <InputIcon>
                                <i class="pi pi-search"></i>
                            </InputIcon>
                            <InputText v-model="search" placeholder="Search" />
                        </IconField>
                    </div>
                </template>

                <Column field="name" header="Full Name" :sortable="true">
                    <template #body="slotProps">
                        {{ slotProps.data.name
                        }}<span v-if="slotProps.data.bn_name"
                    ><br />({{ slotProps.data.bn_name }})</span
                    >
                    </template>
                </Column>
                <Column
                    field="email"
                    header="Contact"
                    style="width: 15rem"
                    :sortable="true"
                >
                    <template #body="slotProps">
                        <Tag
                            v-if="slotProps.data.email"
                            severity="info"
                            icon="pi pi-at"
                            :value="slotProps.data.email"
                            rounded
                        ></Tag>
                        <Tag
                            v-if="slotProps.data.mobile"
                            class="mt-2"
                            severity="info"
                            icon="pi pi-mobile"
                            :value="slotProps.data.mobile"
                            rounded
                        ></Tag>
                    </template>
                </Column>
                <Column field="roles" header="Roles">
                    <template #body="slotProps">
                        {{
                            slotProps.data.roles?.length
                                ? slotProps.data.roles[0].name
                                : 'Not assigned'
                        }}
                    </template>
                </Column>
                <Column field="status" header="Status" :sortable="true">
                    <template #body="{ data }">
                        <Tag :severity="!!data.is_active ? 'success' : 'danger'">
                            {{ !!data.is_active ? 'Active' : 'Inactive' }}
                        </Tag>
                    </template>
                </Column>
                <Column field="id" header="Action" style="width: 120px">
                    <template #body="{ data }">
                        <div
                            v-if="isLoading"
                            class="flex items-center"
                            :style="{
                                    height: '17px',
                                    'flex-grow': '1',
                                    overflow: 'hidden',
                                }"
                        >
                            <Skeleton width="40%" height="1rem" />
                            <Skeleton width="40%" height="1rem" />
                        </div>
                        <div v-else class="flex gap-2">
                            <Button
                                v-if="hasPermission('user_update')"
                                icon="pi pi-pencil"
                                severity="info"
                                size="small"
                                text
                                rounded
                                v-tooltip.top="'Edit User'"
                                @click="openEditModal(data)"
                            />
                            <Button
                                v-if="hasPermission('user_delete')"
                                @click="deleteUser(data.id, data.name)"
                                icon="pi pi-trash"
                                severity="danger"
                                size="small"
                                text
                                rounded
                                v-tooltip.top="'Delete User'"
                            />
                            <Button
                                v-if="hasPermission('user_password')"
                                icon="pi pi-key"
                                severity="warning"
                                size="small"
                                text
                                rounded
                                v-tooltip.top="'Change Password'"
                                @click="openPasswordModal(data)"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </Card>

        <!-- Modal Form -->
        <Dialog
            v-model:visible="showFormModal"
            modal
            :header="formMode === 'create' ? 'Create User' : 'Edit User'"
            :style="{ width: '50rem' }"
        >
            <UserForm
                :mode="formMode"
                :roles="roles"
                :user="user"
                @success="handleFormSuccess"
                @cancel="closeModal"
            />
        </Dialog>

        <!-- Password Modal -->
        <Dialog
            v-model:visible="showPasswordModal"
            modal
            header="Change Password"
            :style="{ width: '30rem' }"
        >
            <UserPasswordForm
                :userId="passwordUser?.id ?? null"
                @success="
                    () => {
                        closePasswordModal();
                        loadLists({
                            rows: perPage.value,
                            page: currentPage.value,
                        });
                    }
                "
                @cancel="closePasswordModal"
            />
        </Dialog>
    </AppLayout>
</template>

<style scoped>
/* keep existing styles if any */
</style>
