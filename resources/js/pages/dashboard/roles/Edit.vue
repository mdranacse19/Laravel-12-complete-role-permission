<script setup lang="ts">
import { onUpdated, useAttrs, ref, defineProps } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Tree from 'primevue/tree';
import Message from 'primevue/message';
import Skeleton from 'primevue/skeleton';
import { useToast } from "primevue/usetoast";
import { showFlashMessage } from '@/Helpers/toast';
import Checkbox from 'primevue/checkbox';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { index as rolesIndex, edit as rolesEdit, update as rolesUpdate } from '@/routes/roles';
import type { TreeNode } from 'primevue/tree';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    rolePermissions: {
        type: Object,
        required: true,
    },
    allPermissions: {
        type: Array as () => TreeNode[],
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
        title: 'Edit Role',
        href: rolesEdit(props.role.id).url,
    },
];

const attrs = useAttrs();
const toast = useToast();
const expandedKeys = ref<Record<string, boolean>>({});
const isProcessing = ref(false);
const form = useForm<{
    roleName: string;
    forPartner: boolean;
    permissions: Record<string, any>;
}>({
    roleName: props.role.name,
    forPartner: props.role.forPartner,
    permissions: props.rolePermissions,
});

onUpdated(function () {
    showFlashMessage(toast, attrs.flash as any)
})

function updateRole() {
    if (isProcessing.value) return;

    isProcessing.value = true;
    form.patch(rolesUpdate(props.role.id).url, {
        preserveScroll: false,
        onSuccess: (res: any) => {
            isProcessing.value = false;
            if(res.props?.flash?.success) {
                toast.add({ severity: 'success', summary: 'Success', detail: res.props.flash.success, group: 'br', life: 3000 });
            }
        },
        onError: (errors: any) => {
            isProcessing.value = false;
            for (var key in errors){
                toast.add({ severity: 'error', summary: 'Error', detail: errors[key], group: 'br', life: 10000 });
            }
        },
    })
}const expandAll = () => {
    for (let node of props.allPermissions) {
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
    <Head title="Edit Role" />

     <AppLayout :breadcrumbs="breadcrumbs">
        <template #pageHeader>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Role
            </h2>

            <Button as="a" :href="rolesIndex().url" label="Go Back" icon="pi pi-arrow-left" size="small" outlined />
        </template>

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <section>
                        <form @submit.prevent="updateRole" class="space-y-6">
                            <div>
                                <div v-if="isProcessing" class="mb-2">
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
                                <div v-if="isProcessing" class="flex align-items-center">
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
                                <div v-if="isProcessing">
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
                                <Skeleton v-if="isProcessing" width="8rem" height="2.5rem" />

                                <Button v-else type="submit" label="Update Role" severity="info" :loading="isProcessing" />

                                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">
                                        Role updated
                                    </p>
                                </Transition>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
