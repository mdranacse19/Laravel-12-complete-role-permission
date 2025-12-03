<script setup lang="ts">
import { ref, computed, watchEffect } from 'vue';
import {
    SidebarGroup,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Settings, ChevronRight, ArrowRight } from 'lucide-vue-next';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';

import { dashboard } from '@/routes';
import { index as formsIndex } from '@/routes/setup/dynamic-form';
import { index as rolesIndex } from '@/routes/roles';
import { index as associationTypesIndex } from '@/routes/setup/association-type';
import { hasPermission } from '@/Helpers/authorization';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();

// Determine if any Setup child route is active (expand collapsible accordingly)
const isSetupActive = computed(() => {
    // associationTypesIndex may expose url() method (object) or be a function returning object.
    try {
        // Current implementation uses associationTypesIndex.url()
        return urlIsActive(associationTypesIndex.url(), page.url, true);
    } catch (e) {
        // Fallback if it's a function returning route object with .url
        return typeof associationTypesIndex === 'function'
            ? urlIsActive(associationTypesIndex().url, page.url, true)
            : false;
    }
});

// Bind to Collapsible open state
const setupOpen = ref(false);
watchEffect(() => {
    setupOpen.value = isSetupActive.value;
});
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton as-child :is-active="urlIsActive(dashboard().url, page.url)" :tooltip="`Dashboard`">
                    <Link :href="dashboard().url">
                        <component :is="LayoutGrid" />
                        <span>Dashboard</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>

            <Collapsible
                v-if="hasPermission(['association_type_access'])"
                as-child
                class="group/collapsible"
                v-model:open="setupOpen"
            >
                <SidebarMenuItem v-if="hasPermission('association_type_access')">
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton :is-active="isSetupActive" :tooltip="`Setup`">
                            <Settings />
                            <span>Setup</span>
                            <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                        </SidebarMenuButton>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <SidebarMenuSub>
                            <SidebarMenuSubItem>
                                <SidebarMenuSubButton as-child :is-active="urlIsActive(associationTypesIndex.url(), page.url)" :tooltip="`Association Type`">
                                    <Link :href="associationTypesIndex.url()">
                                        <component :is="ArrowRight" />
                                        <span>Association Type</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                        </SidebarMenuSub>
                    </CollapsibleContent>
                </SidebarMenuItem>
            </Collapsible>

            <SidebarMenuItem v-if="hasPermission('form_builder_access')">
                <SidebarMenuButton as-child :is-active="urlIsActive(formsIndex().url, page.url, true)" :tooltip="`Forms`">
                    <Link :href="formsIndex().url">
                        <component :is="LayoutGrid" />
                        <span>Dynamic Forms</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
            
            <SidebarMenuItem v-if="hasPermission('role_access')">
                <SidebarMenuButton as-child :is-active="urlIsActive(rolesIndex().url, page.url, true)" :tooltip="`Roles`">
                    <Link :href="rolesIndex().url">
                        <component :is="LayoutGrid" />
                        <span>Roles</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
