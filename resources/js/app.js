import './bootstrap';
import { initializeOrderProductRemoval } from './order-product-removal';
import { initializeProductAvailability } from './product-availability';
import { showFlashToasts } from './sweet-alert';
import { initializeWaiterAvailability } from './waiter-availability';
import { initializeKitchenPanel } from './kitchen-panel';
import { initializeWaiterKitchenNotifications } from './waiter-kitchen-notifications';

document.addEventListener('DOMContentLoaded', () => {
    showFlashToasts();
    initializeProductAvailability();
    initializeOrderProductRemoval();
    initializeWaiterAvailability();
    initializeKitchenPanel();
    initializeWaiterKitchenNotifications();
});
