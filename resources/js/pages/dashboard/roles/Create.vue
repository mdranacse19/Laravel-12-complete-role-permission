<script setup lang="ts">
import { onUpdated, useAttrs, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Tree from 'primevue/tree';
import Message from 'primevue/message';
import Skeleton from 'primevue/skeleton';
import { useToast } from "primevue/usetoast";
import { showFlashMessage, type FlashMessages } from '@/Helpers/toast';
import Checkbox from 'primevue/checkbox';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { index as rolesIndex, create as rolesCreate, store as rolesStore } from '@/routes/roles';
import type { TreeNode } from 'primevue/tree';

const props = defineProps({
    allPermissions: {
        type: Object,
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Roles',
        href: rolesIndex().url,
    },
    {
        title: 'Create Role',
        href: rolesCreate().url,
    },
];

const attrs = useAttrs();
const toast = useToast();
const isLoading = ref(false);
const expandedKeys = ref<Record<string, boolean>>({});
const form = useForm({
    roleName: '',
    forPartner: false,
    permissions: {} as Record<string, { checked: boolean; partialChecked: boolean }>,
});

onUpdated(function () {
    showFlashMessage(toast, attrs.flash as FlashMessages)
})

function addNewRole() {
    form.post(rolesStore().url, {
        preserveScroll: true,
        onSuccess: (res: any) => {
            if(res.props?.flash?.success) {
                toast.add({ severity: 'success', summary: 'Success', detail: res.props.flash.success, group: 'br', life: 3000 });
            }
        },
        onError: (errors: any) => {
            for (var key in errors){
                toast.add({ severity: 'error', summary: 'Error', detail: errors[key], group: 'br', life: 10000 });
            }
        },
    })
}

const expandAll = () => {
    for (let node of Object.values(props.allPermissions)) {
        expandNode(node);
    }

    expandedKeys.value = { ...expandedKeys.value };
};

const collapseAll = () => {
    expandedKeys.value = {};
};

const expandNode = (node: any) => {
    if (node.children && node.children.length) {
        expandedKeys.value[node.key] = true;

        for (let child of node.children) {
            expandNode(child);
        }
    }
};

const selectAccessPermission = (node: any) => {
    // check if the node is a group name
    if (! node?.module_key) {
        return;
    }

    const accessPermission = node.module_key + '_access';

    // do nothing if the node is access permission
    if (node.key === accessPermission) {
        return;
    }

    // add the access permission to the form
    form.permissions[accessPermission] = {
        'checked': true,
        'partialChecked': false,
    };
};

const verifyAccessPermission = (node: any) => {
    // check if the node is a group name
    if (! node?.module_key) {
        return;
    }

    const accessPermission = node.module_key + '_access';

    // do nothing if the node is not access permission
    if (node.key !== accessPermission) {
        return;
    }

    // check if any other permission was set on the form
    let hasGroupPermission = Object.keys(form.permissions).some(key => {
        return key.startsWith(node.module_key)
    })

    // warn user and set the access permission to the form
    if(hasGroupPermission){
        toast.add({ severity: 'warn', summary: 'Warning', detail: 'A module must have the "Access" permission selected for other permissions of that module to take effect.', group: 'br', life: 10000 });

        form.permissions[accessPermission] = {
            'checked': true,
            'partialChecked': false,
        };
    }
}
</script>

<template>
    <Head title="New Role" />

     <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageHeader>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                New Role
            </h2>

            <Button as="a" :href="rolesIndex().url" label="Go Back" icon="pi pi-arrow-left" size="small" outlined />
        </template>

        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <form @submit.prevent="addNewRole" class="space-y-6">
                    <div>
                        <div v-if="isLoading" class="mb-2">
                            <Skeleton width="8rem" height="1.5rem" class="mb-2" />
                            <Skeleton width="100%" height="2.5rem" />
                        </div>
                        <div v-else>
                            <label for="role_name">Role Name</label>
                            <InputText id="role_name" type="text" class="mt-1 block w-full" v-model="form.roleName"
                            placeholder="Enter a role name" autofocus autocomplete="off" />

                            <Message v-if="form.errors.roleName" severity="error" variant="simple" size="small" class="mt-2">{{ form.errors.roleName }}</Message>
                        </div>
                    </div>

                    <div>
                        <div v-if="isLoading" class="flex align-items-center">
                            <Skeleton width="1.25rem" height="1.25rem" class="mr-2" />
                            <Skeleton width="10rem" height="1.5rem" />
                        </div>
                        <div v-else>
                            <div class="flex align-items-center">
                                <Checkbox v-model="form.forPartner" inputId="for_rmg" name="for_rmg" :binary="true" />
                                <label for="for_rmg" class="ml-2"> Role For RMG </label>
                            </div>

                            <Message v-if="form.errors.forPartner" severity="error" variant="simple" size="small" class="mt-2">{{ form.errors.forPartner }}</Message>
                        </div>
                    </div>

                    <div class="card flex flex-col items-cente">
                        <div v-if="isLoading">
                            <Skeleton width="10rem" height="1.5rem" class="mb-4" />
                            <div class="flex flex-wrap gap-2 mb-4">
                                <Skeleton width="8rem" height="2.2rem" />
                                <Skeleton width="9rem" height="2.2rem" />
                            </div>
                            <div class="space-y-2">
                                <Skeleton width="100%" height="2.5rem" />
                                <Skeleton width="95%" height="2rem" class="ml-4" />
                                <Skeleton width="95%" height="2rem" class="ml-4" />
                                <Skeleton width="100%" height="2.5rem" />
                                <Skeleton width="95%" height="2rem" class="ml-4" />
                                <Skeleton width="95%" height="2rem" class="ml-4" />
                                <Skeleton width="100%" height="2.5rem" />
                            </div>
                        </div>
                        <div v-else>
                            <InputLabel for="permissions" value="Permissions" required/>

                            <div class="flex flex-wrap gap-2 mb-4">
                                <Button type="button" icon="pi pi-plus" size="small" plain text outlined label="Expand All" @click="expandAll" />
                                <Button type="button" icon="pi pi-minus" size="small" plain text outlined label="Collapse All" @click="collapseAll" />
                            </div>

                            <Tree v-model:selectionKeys="form.permissions" v-model:expandedKeys="expandedKeys" :value="allPermissions"
                                @nodeSelect="selectAccessPermission" @nodeUnselect="verifyAccessPermission" selectionMode="checkbox" class="mt-1 block w-full select-none">
                                <template #default="slotProps">
                                    <template v-if="'children' in slotProps.node">
                                        <strong>{{ slotProps.node.label }}</strong>
                                    </template>
                                    <template v-else>
                                        {{ slotProps.node.label }}
                                    </template>
                                    <br>
                                    <i v-if="slotProps.node.description" class="text-gray-400 text-sm">{{ slotProps.node.description }}</i>
                                </template>
                            </Tree>

                            <Message v-if="form.errors.permissions" severity="error" variant="simple" size="small" class="mt-2">{{ form.errors.permissions }}</Message>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Skeleton v-if="isLoading" width="8rem" height="2.5rem" />
                        <Button v-else type="submit" label="Add Role" severity="info" :loading="form.processing" />
                    </div>
                </form>
            </section>
        </div>
    </AppLayout>
</template>
