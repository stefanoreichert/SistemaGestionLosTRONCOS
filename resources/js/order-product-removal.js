import { confirmWarning } from './sweet-alert';

export const initializeOrderProductRemoval = () => {
    document.querySelectorAll('[data-confirm-remove-product]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const result = await confirmWarning({
                html: 'Está por quitar este producto del pedido actual.<br><br>¿Desea continuar?',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Quitar producto',
            });

            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
};
