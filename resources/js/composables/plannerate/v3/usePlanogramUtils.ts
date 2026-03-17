export function shouldShowDeleteConfirm(itemType: string = 'section'): boolean {
    const storageKey = `planogram-delete-confirm-${itemType}`;
    const expiryTime = localStorage.getItem(storageKey);

    if (!expiryTime) {
        return true;
    }

    const expiry = parseInt(expiryTime, 10);
    const now = Date.now();

    if (now > expiry) {
        localStorage.removeItem(storageKey);
        return true;
    }

    return false;
}
