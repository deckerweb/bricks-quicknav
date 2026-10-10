/* Native dialog preserves Escape, focus containment, and focus return. */
(() => {
  let opener = null;
  document.querySelectorAll('[data-bxqn-open]').forEach(button => {
    button.addEventListener('click', () => {
      const dialog = document.getElementById(button.dataset.bxqnOpen);
      if (!dialog || typeof dialog.showModal !== 'function') return;
      opener = button;
      dialog.showModal();
      dialog.querySelector('[data-bxqn-close]').focus();
    });
  });
  document.querySelectorAll('#bxqn-history').forEach(dialog => {
    dialog.querySelector('[data-bxqn-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => { if (opener) opener.focus(); });
  });
})();
