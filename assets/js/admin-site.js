(() => {
  'use strict';
  document.querySelector('[data-close-toast]')?.addEventListener('click', event => event.currentTarget.closest('.site-toast').remove());
  document.querySelectorAll('[data-site-form]').forEach(form => {
  const file = form.querySelector('#site-imagem');
  const preview = form.querySelector('#sitePreviewImage');
  const empty = form.querySelector('#sitePreviewEmpty');
  const original = preview?.getAttribute('src');
  let previewUrl;
  function showError(input, message) {
    input.setAttribute('aria-invalid', message ? 'true' : 'false');
    document.getElementById(input.id + '-error').textContent = message;
    return !message;
  }
  function validate(input) {
    const value = input.value.trim();
    let message = '';
    if (input.required && !value) message = 'Preencha este campo.';
    else if (input.maxLength > 0 && [...input.value].length > input.maxLength) message = `Use no máximo ${input.maxLength} caracteres.`;
    else if (input.type === 'number' && (!/^\d+$/.test(value) || Number(value)>9999)) message = 'Informe uma ordem de 0 a 9999.';
    else if (input.type === 'file' && input.files[0]) {
      if (input.files[0].size > 5 * 1024 * 1024) message = 'A imagem deve ter no máximo 5 MB.';
      else if (!['image/jpeg','image/png','image/webp'].includes(input.files[0].type)) message = 'Envie uma imagem JPG, PNG ou WebP válida.';
    }
    return showError(input, message);
  }
  const fields = [...form.querySelectorAll('input:not([type=hidden]),select,textarea')];
  fields.forEach(input => {
    const update = () => {
      if (input.hasAttribute('data-char-count')) document.getElementById(input.id+'-count').textContent = `${[...input.value].length} / ${input.maxLength}`;
      if (input.getAttribute('aria-invalid') === 'true') validate(input);
    };
    input.addEventListener('input',update);
    input.addEventListener('blur',()=>validate(input));
    update();
  });
  file?.addEventListener('change', () => {
    if (previewUrl) URL.revokeObjectURL(previewUrl);
    if (!file.files[0] || !validate(file)) {
      preview.hidden = !original;
      empty.hidden = !!original;
      if (original) preview.src = original;
      else preview.removeAttribute('src');
      return;
    }
    previewUrl = URL.createObjectURL(file.files[0]);
    preview.src = previewUrl;
    preview.hidden = false;
    empty.hidden = true;
  });
  form.addEventListener('submit',event => {
    const invalid = fields.filter(input=>!validate(input));
    if (invalid.length) { event.preventDefault(); invalid[0].focus(); }
  });
  });
})();
