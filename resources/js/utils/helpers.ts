import moment from "moment";

/**
 * Format date into any format you want
 * @param date - JS Date, string, or null
 * @param format - moment.js format (default: YYYY-MM-DD)
 * @returns formatted date string or null
 */
export const formatDate = (date: any, format = "YYYY-MM-DD") => {
    if (!date) return null;

    return moment(date).format(format);
};

/**
 * Check if a value is empty
 */
export const isEmpty = (value: any): boolean => {
    if (value === null || value === undefined) return true;
    if (typeof value === "string" && value.trim() === "") return true;
    if (Array.isArray(value) && value.length === 0) return true;

    return false;
};

/**
 * Generate random string (useful for unique keys)
 */
export const randomString = (length = 10): string => {
    return Math.random().toString(36).substring(2, 2 + length);
};

/**
 * Safely parse JSON
 */
export const safeJsonParse = (value: string) => {
    try {
        return JSON.parse(value);
    } catch {
        return null;
    }
};
