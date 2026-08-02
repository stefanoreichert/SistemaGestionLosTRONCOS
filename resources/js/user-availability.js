import { confirmWarning } from './sweet-alert';

const setProcessing = (form, isProcessing) => {
    form.querySelector('[data-user-availability-toggle]')?.toggleAttribute('disabled', isProcessing);
    form.querySelector('[data-user-availability-spinner]')?.classList.toggle('hidden', !isProcessing);
};

export const initializeUserAvailability = () => {
    document.querySelectorAll('[data-user-availability-form]').forEach((form) => {
        const toggle = form.querySelector('[data-user-availability-toggle]');

        toggle?.addEventListener('change', async () => {
            const isActivating = toggle.checked;
            const result = await confirmWarning({
                html: isActivating
                    ? 'Este usuario volverá a poder ingresar al sistema.<br><br>¿Desea continuar?'
                    : 'Este usuario dejará de poder ingresar al sistema.<br><br>No será eliminado ni se perderá su historial.<br><br>¿Desea continuar?',
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
