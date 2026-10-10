/** Native Settings API form: accessible sections and local unsaved-change state. */
(() => {
  'use strict';
  const form = document.getElementById('bxqn-settings-form');
  const nav = document.querySelector('.bxqn-tabs');
  if (!form || !nav) return;
  const tabs = Array.from(nav.querySelectorAll('a'));
  const panels = tabs.map(tab => document.getElementById(tab.hash.slice(1)));
  const status = document.getElementById('bxqn-unsaved-status');
  const bar = form.querySelector('.bxqn-savebar');
  // Compare actual successful option controls, including values in hidden sections.
  const snapshot = () => JSON.stringify(Array.from(new FormData(form)).filter(([key]) => key.startsWith('ddw_bxqn_settings[')));
  const baseline = snapshot();
  let submitting = false;
  const dirty = () => snapshot() !== baseline;
  const update = () => {
    const changed = dirty();
    status.textContent = changed ? status.dataset.dirty : status.dataset.saved;
    bar.classList.toggle('is-dirty', changed);
  };
  const select = (index, focus = false) => {
    tabs.forEach((tab, i) => {
      tab.classList.toggle('nav-tab-active', i === index);
      tab.setAttribute('aria-selected', String(i === index));
      tab.tabIndex = i === index ? 0 : -1;
      panels[i].hidden = i !== index;
    });
    if (focus) tabs[index].focus();
    history.replaceState(null, '', tabs[index].hash);
    // WordPress preserves the selected section on its native post/redirect flow.
    const referer = form.querySelector('input[name="_wp_http_referer"]');
    if (referer) referer.value = location.pathname + location.search + tabs[index].hash;
  };
  nav.setAttribute('role', 'tablist');
  tabs.forEach((tab, i) => {
    tab.setAttribute('role', 'tab');
    tab.setAttribute('aria-controls', panels[i].id);
    panels[i].setAttribute('role', 'tabpanel');
    panels[i].tabIndex = 0;
    tab.addEventListener('click', event => { event.preventDefault(); select(i); });
    tab.addEventListener('keydown', event => {
      let next;
      if (event.key === 'ArrowRight') next = (i + 1) % tabs.length;
      if (event.key === 'ArrowLeft') next = (i + tabs.length - 1) % tabs.length;
      if (event.key === 'Home') next = 0;
      if (event.key === 'End') next = tabs.length - 1;
      if (next !== undefined) { event.preventDefault(); select(next, true); }
    });
  });
  document.querySelector('.bxqn-settings').classList.add('bxqn-enhanced');
  select(Math.max(0, tabs.findIndex(tab => tab.hash === location.hash)));
  update();
  form.addEventListener('input', update);
  form.addEventListener('change', update);
  form.addEventListener('submit', () => { submitting = true; });
  form.addEventListener('invalid', event => {
    const index = panels.findIndex(panel => panel.contains(event.target));
    if (index >= 0) select(index);
  }, true);
  window.addEventListener('beforeunload', event => {
    if (!submitting && dirty()) { event.preventDefault(); event.returnValue = ''; }
  });
})();
