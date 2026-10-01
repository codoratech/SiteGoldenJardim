(() => {
  const moneyNumber = value => {
    let text = value.replace(/R\$|\s/g, '');
    if (text.includes(',')) text = text.replace(/\./g, '').replace(',', '.');
    return /^\d+(?:\.\d{1,2})?$/.test(text) ? Number(text) : NaN;
  };
  document.querySelectorAll('[data-money]').forEach(input => {
    const format = () => { const n=moneyNumber(input.value); if (Number.isFinite(n)) input.value='R$ '+n.toFixed(2).replace('.', ','); };
    format();
    input.addEventListener('focus', () => { input.value=input.value.replace('R$', '').trim(); });
    input.addEventListener('blur', format);
  });
  document.querySelectorAll('input[type="tel"]').forEach(input => {
    input.addEventListener('input', () => {
      const digits=input.value.replace(/\D/g,'').slice(0,11);
      input.value=digits.length>10 ? digits.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3') : digits.length>6 ? digits.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3') : digits.length>2 ? digits.replace(/^(\d{2})(\d+)$/, '($1) $2') : digits;
    });
  });
  document.querySelectorAll('[data-managed-form]').forEach(form => {
    const fields=[...form.querySelectorAll('input:not([type="hidden"]),select,textarea')];
    const validate = input => {
      let message=''; const value=input.value.trim();
      if (input.required && !value) message='Preencha este campo.';
      else if (value && input.type==='email' && input.validity.typeMismatch) message='Informe um e-mail válido.';
      else if (value && input.type==='tel' && ![10,11].includes(value.replace(/\D/g,'').length)) message='Informe o DDD e um telefone com 10 ou 11 dígitos.';
      else if (value && input.hasAttribute('data-money') && !Number.isFinite(moneyNumber(value))) message='Informe um valor válido, como 150,00.';
      else if (value && input.type==='number' && (!/^\d+$/.test(value) || !input.validity.valid)) message='Informe um número inteiro dentro do intervalo permitido.';
      else if (value && input.type==='password' && value.length<8) message='Use pelo menos 8 caracteres.';
      else if (value && !input.validity.valid) message='Confira o valor informado.';
      input.setAttribute('aria-invalid',message ? 'true' : 'false');
      const error=document.getElementById(input.id+'-error'); if(error) error.textContent=message;
      return !message;
    };
    fields.forEach(input => { input.addEventListener('blur',()=>validate(input)); input.addEventListener('input',()=>{if(input.getAttribute('aria-invalid')==='true')validate(input);}); });
    form.addEventListener('submit', event => {
      const invalid=fields.filter(input=>!validate(input));
      if(invalid.length){event.preventDefault();invalid[0].focus();return;}
      form.querySelectorAll('[data-money]').forEach(input=>{input.value=moneyNumber(input.value).toFixed(2);});
    });
  });
  const dialog=document.getElementById('deleteModal');
  if (!dialog) return;
  let pendingForm, trigger;
  document.querySelectorAll('[data-delete-form]').forEach(form=>form.addEventListener('submit',event=>{
    event.preventDefault(); pendingForm=form; trigger=event.submitter;
    document.getElementById('deleteModalText').textContent=form.dataset.confirmMsg || 'Deseja excluir este registro?';
    dialog.showModal(); document.getElementById('cancelDeleteBtn').focus();
  }));
  document.getElementById('cancelDeleteBtn').addEventListener('click',()=>dialog.close());
  document.getElementById('confirmDeleteBtn').addEventListener('click',()=>{if(pendingForm) HTMLFormElement.prototype.submit.call(pendingForm);});
  dialog.addEventListener('close',()=>trigger?.focus());
  dialog.addEventListener('click',event=>{if(event.target===dialog){const r=dialog.getBoundingClientRect();if(event.clientX<r.left || event.clientX>r.right || event.clientY<r.top || event.clientY>r.bottom)dialog.close();}});
})();
