const NEXT = { PENDING: ['IN_PREPARATION', 'Iniciar preparación'], IN_PREPARATION: ['READY', 'Marcar listo'], READY: ['RETIRED', 'Retirar'] };
const LABEL = { PENDING: 'Pendiente', IN_PREPARATION: 'En preparación', READY: 'Listo' };

const el = (tag, classes, text) => { const node = document.createElement(tag); node.className = classes; if (text !== undefined) node.textContent = text; return node; };
const beep = () => { const context = new AudioContext(); const oscillator = context.createOscillator(); oscillator.connect(context.destination); oscillator.start(); oscillator.stop(context.currentTime + 0.18); };

export function initializeKitchenPanel() {
    const root = document.getElementById('kitchen-panel');
    if (!root) return;
    const grid = document.getElementById('kitchen-orders');
    const empty = document.getElementById('kitchen-empty');
    const error = document.getElementById('kitchen-error');
    let known = null; let sound = false; let polling = false;
    document.getElementById('kitchen-enable-sound')?.addEventListener('click', (event) => { sound = true; event.currentTarget.textContent = 'Sonido activado'; beep(); });
    const updateClock = () => { const now = new Date(); document.getElementById('kitchen-clock').textContent = now.toLocaleTimeString('es-PY'); document.getElementById('kitchen-date').textContent = now.toLocaleDateString('es-PY', { weekday: 'long', day: 'numeric', month: 'long' }); };
    const render = (orders) => {
        const ids = new Set(orders.map((order) => order.id));
        if (known && sound && orders.some((order) => !known.has(order.id))) beep();
        known = ids; grid.replaceChildren(); empty.classList.toggle('hidden', orders.length > 0);
        orders.forEach((order) => {
            const card = el('article', `card border-2 bg-base-100 shadow ${order.status === 'READY' ? 'border-success' : order.status === 'IN_PREPARATION' ? 'border-warning' : 'border-base-300'}`);
            const body = el('div', 'card-body gap-3');
            const heading = el('div', 'flex items-center justify-between gap-2'); heading.append(el('h2', 'card-title text-2xl', `Mesa ${order.table_number}`), el('span', 'badge badge-lg badge-outline', LABEL[order.status])); body.append(heading);
            body.append(el('p', 'text-sm text-base-content/60', order.waiter_name));
            const list = el('ul', 'space-y-2'); order.items.forEach((item) => list.append(el('li', 'flex justify-between rounded bg-base-200 px-3 py-2 text-lg', `${item.quantity} × ${item.name}`))); body.append(list);
            const next = NEXT[order.status]; if (next) { const button = el('button', 'btn btn-primary mt-auto', next[1]); button.addEventListener('click', async () => { button.disabled = true; try { await window.axios.patch(root.dataset.updateUrl.replace('__ORDER__', order.id), { status: next[0] }, { headers: { 'X-CSRF-TOKEN': root.dataset.csrf } }); await poll(); } finally { button.disabled = false; } }); body.append(button); }
            card.append(body); grid.append(card);
        });
    };
    const poll = async () => { if (polling || document.hidden) return; polling = true; try { const { data } = await window.axios.get(root.dataset.ordersUrl); render(data.orders); error.classList.add('hidden'); } catch (_) { error.textContent = 'No se pudo actualizar el panel. Se reintentará automáticamente.'; error.classList.remove('hidden'); } finally { polling = false; } };
    updateClock(); setInterval(updateClock, 1000); poll(); setInterval(poll, 5000);
}
