import './bootstrap';
import { initializeOrderProductRemoval } from './order-product-removal';
import { initializeDailyClosurePrint, initializeDailyReportClosure } from './daily-report-closure';
import { initializeProductAvailability } from './product-availability';
import { showFlashToasts } from './sweet-alert';
import { initializeWaiterAvailability } from './waiter-availability';
import { initializeUserAvailability } from './user-availability';

document.addEventListener('DOMContentLoaded', () => {
    showFlashToasts();
    initializeDailyReportClosure();
    initializeDailyClosurePrint();
    initializeProductAvailability();
    initializeOrderProductRemoval();
    initializeWaiterAvailability();
    initializeUserAvailability();
});
