import './bootstrap';
import { initializeOrderProductRemoval } from './order-product-removal';
import { initializeProductAvailability } from './product-availability';
import { showFlashToasts } from './sweet-alert';
import { initializeWaiterAvailability } from './waiter-availability';

document.addEventListener('DOMContentLoaded', () => {
    showFlashToasts();
    initializeProductAvailability();
    initializeOrderProductRemoval();
    initializeWaiterAvailability();
});
