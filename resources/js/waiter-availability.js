import { confirmWarning } from './sweet-alert';

const setProcessing = (form, isProcessing) => {
    const toggle = form.querySelector('[data-waiter-availability-toggle]');
    const spinner = form.querySelector('[data-waiter-availability-spinner]');

    if (toggle) {
        toggle.disabled = isProcessing;
    }

    spinner?.classList.toggle('hidden', !isProcessing);
};

export const initializeWaiterAvailability = () => {
    document.querySelectorAll('[data-waiter-availability-form]').forEach((form) => {
        const toggle = form.querySelector('[data-waiter-availability-toggle]');

        toggle?.addEventListener('change', async () => {
            const isActivating = toggle.checked;
            const result = await confirmWarning({
                html: isActivating
                    ? 'Este mozo volverá a estar disponible para futuras asignaciones.<br><br>¿Desea continuar?'
                    : 'Este mozo dejará de estar disponible para futuras asignaciones.<br><br>No será eliminado del sistema ni del historial.<br><br>¿Desea continuar?',
                cancelButtonText: 'Cancelar',
                confirmButtonText: isActivating ? 'Activar' : 'Desactivar',
            });

            if (!result.isConfirmed) {
                toggle.checked = !isActivating;
                return;
            }

            setProcessing(form, true);
            form.submit();
        });
    });
};
