import Swal from 'sweetalert2';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});

export const confirmWarning = (options) => Swal.fire({
    icon: 'warning',
    title: 'ATENCIÓN',
    showCancelButton: true,
    reverseButtons: true,
    focusCancel: true,
    ...options,
});

export const showToast = (icon, title) => Toast.fire({ icon, title });

export const showFlashToasts = () => {
    const flashMessages = document.getElementById('flash-messages');

    if (!flashMessages) {
        return;
    }

    [
        ['success', flashMessages.dataset.success],
        ['error', flashMessages.dataset.error],
        ['warning', flashMessages.dataset.warning],
    ].forEach(([icon, message]) => {
        if (message) {
            showToast(icon, message);
        }
    });
};
