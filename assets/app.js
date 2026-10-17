document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', event => {
      if (!window.confirm(form.dataset.confirm || 'Are you sure?')) event.preventDefault();
    });
  });
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
      const password = form.querySelector('#password');
      const confirm = form.querySelector('#confirm_password');
      if (password && confirm && password.value !== confirm.value) {
        confirm.setCustomValidity('Passwords do not match.');
        confirm.reportValidity();
        event.preventDefault();
      } else if (confirm) {
        confirm.setCustomValidity('');
      }
    });
    const confirm = form.querySelector('#confirm_password');
    if (confirm) confirm.addEventListener('input', () => confirm.setCustomValidity(''));
  });
});
