import Swal from 'sweetalert2';

const beep = () => { const context = new AudioContext(); const oscillator = context.createOscillator(); oscillator.connect(context.destination); oscillator.start(); oscillator.stop(context.currentTime + 0.18); };

export function initializeWaiterKitchenNotifications() {
    const root = document.getElementById('waiter-kitchen-notifications');
    if (!root) return;
    let since = root.dataset.since; let sound = false; let polling = false;
    root.querySelector('[data-enable-kitchen-sound]')?.addEventListener('click', (event) => { sound = true; event.currentTarget.textContent = 'Sonido activado'; beep(); });
    const poll = async () => { if (polling || document.hidden) return; polling = true; try { const { data } = await window.axios.get(root.dataset.url, { params: { since } }); since = data.server_now; for (const order of data.orders) { if (sound) beep(); await Swal.fire({ icon: 'success', title: `Pedido listo · Mesa ${order.table_number}`, text: 'El pedido está listo para retirar.', confirmButtonText: 'Entendido' }); } } finally { polling = false; } };
    setInterval(poll, 5000);
}
