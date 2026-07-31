import { confirmWarning } from './sweet-alert';

const setProcessing = (form, isProcessing) => {
    const toggle = form.querySelector('[data-product-availability-toggle]');
    const spinner = form.querySelector('[data-product-availability-spinner]');

    if (toggle) {
        toggle.disabled = isProcessing;
    }

    spinner?.classList.toggle('hidden', !isProcessing);
};

export const initializeProductAvailability = () => {
    document.querySelectorAll('[data-product-availability-form]').forEach((form) => {
        const toggle = form.querySelector('[data-product-availability-toggle]');

        toggle?.addEventListener('change', async () => {
            const isActivating = toggle.checked;
            const result = await confirmWarning({
                html: isActivating
                    ? 'Este producto volverá a estar disponible para nuevas ventas.<br><br>¿Desea continuar?'
                    : 'Este producto dejará de estar disponible para nuevas ventas.<br><br>No será eliminado del sistema ni del historial.<br><br>¿Desea continuar?',
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
