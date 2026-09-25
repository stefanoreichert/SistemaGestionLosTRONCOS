import { confirmWarning } from './sweet-alert';

export const initializeDailyReportClosure = () => {
    document.querySelectorAll('[data-daily-report-closure-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();

            const result = await confirmWarning({
                html: 'Se imprimirá el resumen actual y comenzará un nuevo período diario en cero.<br><br>Los pedidos, tickets, ventas y el historial no serán eliminados.<br><br>¿Desea continuar?',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Imprimir y cerrar día',
            });

            if (!result.isConfirmed) {
                return;
            }

            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('loading');
            }

            form.dataset.confirmed = 'true';
            form.submit();
        });
    });
};

export const initializeDailyClosurePrint = () => {
    const printPage = document.querySelector('[data-daily-closure-print]');

    if (!printPage) {
        return;
    }

    document.querySelector('[data-print-daily-closure]')
        ?.addEventListener('click', () => window.print());

    window.print();
};
