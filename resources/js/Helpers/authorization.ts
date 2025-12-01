import { usePage } from '@inertiajs/vue3';

const $page = usePage();

/**
 * Roles defined on this application.
 */
export const SUPERADMIN = 'Super Admin';
export const ADMIN = 'Admin';
export const PARTNER = 'Partner';
export const TRAINER = 'Trainer';
export const MANAGER = 'Manager';
export const MASTERCRAFT = 'Mastercraft';
export const FRONTLINE = 'Frontline';

/**
 * Check if the authenticated user has a role.
 *
 * @param role - Name of the role
 * @returns boolean
 */
export const hasRole = (role: string): boolean => {
    return $page.props.auth?.roles?.includes(role) ?? false;
};

/**
 * Check if the authenticated user has a/any of the provided permission.
 *
 * @param permission - Name of the permission or array of permissions
 * @returns boolean
 */
export function hasPermission(permission: string | string[]): boolean {
    if ($page.props.auth?.roles?.includes(SUPERADMIN)) {
        return true;
    }

    if (Array.isArray(permission)) {
        return permission.some(name => $page.props.auth?.permissions?.includes(name) ?? false);
    }

    return $page.props.auth?.permissions?.includes(permission) ?? false;
}

/**
 * Checks if the authenticated user has all of the provided permissions.
 *
 * @param permissions - List of permissions to check
 * @returns boolean
 */
export function hasEveryPermission(permissions: string[]): boolean {
    if ($page.props.auth?.roles?.includes(SUPERADMIN)) {
        return true;
    }

    return permissions.every(name => $page.props.auth?.permissions?.includes(name) ?? false);
}
