 // Sidebar toggle (giữ nguyên nếu bạn đã có)
  document.querySelector('.sidebar-toggle')?.addEventListener('click', () => {
    document.getElementById('appSidebar')?.classList.toggle('open');
  });

  const table = document.getElementById('salary-table');
  const panel = document.getElementById('salary-detail');

  function formatCurrency(n) {
    const num = Number(n);
    if (isNaN(num)) return n;
    return num.toLocaleString(undefined, { maximumFractionDigits: 2 });
  }

  function fillPanel(tr) {
    const d = tr.dataset;
    document.getElementById('d-id').textContent        = d.id || '';
    document.getElementById('d-empid').textContent     = d.empid || '';
    document.getElementById('d-hours').textContent     = d.hours || '';
    document.getElementById('d-hourly').textContent    = formatCurrency(d.hourly || '');
    document.getElementById('d-base').textContent      = formatCurrency(d.basesalary || '');
    document.getElementById('d-bonus').textContent     = formatCurrency(d.bonus || '');
    document.getElementById('d-deduction').textContent = formatCurrency(d.deduction || '');
    document.getElementById('d-total').textContent     = formatCurrency(d.total || '');
  }

  // click vào Employee ID -> mở panel chi tiết
  table.addEventListener('click', function (e) {
    const link = e.target.closest('.emp-link');
    if (!link) return;
    e.preventDefault();
    const tr = link.closest('tr');
    fillPanel(tr);
    panel.classList.remove('hidden');
    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  // Xóa dòng
  table.querySelectorAll('.remove-row').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const tr = e.target.closest('tr');
      tr?.parentNode?.removeChild(tr);
      // TODO: show toast nếu bạn đã có hệ thống #toasts
    });
  });

  // Filter theo Employee ID
  document.getElementById('apply-filter')?.addEventListener('click', () => {
    const empId = (document.getElementById('filter-emp-id')?.value || '').trim();
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
      const rEmp = row.querySelector('.emp-link')?.textContent.trim() || row.children[2].textContent.trim();
      row.style.display = (!empId || rEmp.includes(empId)) ? '' : 'none';
    });
  });