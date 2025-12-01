<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
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
import { BookOpen, Folder, LayoutGrid, Settings, ChevronRight, ArrowRight } from 'lucide-vue-next';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';

import { dashboard } from '@/routes';
import { index as formsIndex } from '@/routes/setup/form-builder';
import { index as rolesIndex } from '@/routes/roles';
import { index as associationTypesIndex } from '@/routes/setup/association-type';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton as-child :is-active="urlIsActive(dashboard().url, page.url)" :tooltip="`Dashboard`">
                    <Link :href="dashboard().url">
                        <component :is="LayoutGrid" />
                        <span>Dashboard</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>

            <Collapsible as-child class="group/collapsible">
                <SidebarMenuItem>
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton :tooltip="`Setup`">
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

            <SidebarMenuItem>
                <SidebarMenuButton as-child :is-active="urlIsActive(formsIndex().url, page.url)" :tooltip="`Forms`">
                    <Link :href="formsIndex().url">
                        <component :is="LayoutGrid" />
                        <span>Dynamic Forms</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
            <SidebarMenuItem>
                <SidebarMenuButton as-child :is-active="urlIsActive(rolesIndex().url, page.url)" :tooltip="`Roles`">
                    <Link :href="rolesIndex().url">
                        <component :is="LayoutGrid" />
                        <span>Roles</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
