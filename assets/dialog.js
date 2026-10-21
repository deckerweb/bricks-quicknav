/* Native dialog methods plus a real history link when JavaScript cannot open it. */
(() => {
  if (document.documentElement.dataset.bxqnDialogReady) return;
  document.documentElement.dataset.bxqnDialogReady = '1';
  const prototype = window.HTMLDialogElement && window.HTMLDialogElement.prototype;
  if (!prototype || typeof prototype.showModal !== 'function') return;
  const show = prototype.showModal;
  const close = prototype.close;
  const openers = new WeakMap();
  document.addEventListener('click', event => {
    const target = event.target instanceof Element ? event.target : null;
    if (!target) return;
    const opener = target.closest('[data-bxqn-open]');
    if (opener) {
      const dialog = document.getElementById(opener.dataset.bxqnOpen);
      if (!dialog) return;
      try {
        dialog.hidden = false;
        show.call(dialog);
        if (!dialog.open) throw new Error('Dialog did not open');
        openers.set(dialog, opener);
        event.preventDefault();
        dialog.querySelector('[data-bxqn-close]')?.focus();
      } catch (_) { dialog.hidden = !dialog.open; }
      return;
    }
    const button = target.closest('[data-bxqn-close]');
    const dialog = button && button.closest('#bxqn-history');
    if (dialog && dialog.open) { close.call(dialog); event.preventDefault(); }
    if (target.id === 'bxqn-history' && target.open) {
      const box = target.getBoundingClientRect();
      if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) close.call(target);
    }
  }, true);
  document.addEventListener('close', event => {
    const dialog = event.target;
    if (dialog.id !== 'bxqn-history') return;
    dialog.hidden = true;
    const opener = openers.get(dialog);
    if (opener && opener.isConnected) opener.focus();
  }, true);
})();
