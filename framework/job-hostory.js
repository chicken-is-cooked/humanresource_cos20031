  // Sidebar toggle (nếu bạn đã có sẵn thì giữ nguyên)
  document.querySelector('.sidebar-toggle')?.addEventListener('click', () => {
    document.getElementById('appSidebar')?.classList.toggle('open');
  });

  const jhTable   = document.getElementById('jh-table');
  const jhDetail  = document.getElementById('jh-detail');

  function fillJHDetail(tr) {
    const td = (i) => tr.children[i].textContent.trim();
    document.getElementById('d-jh-id').textContent   = td(1);
    document.getElementById('d-jh-emp').textContent  = td(2);
    document.getElementById('d-jh-pos').textContent  = td(5);
    document.getElementById('d-jh-dept').textContent = td(6);
    document.getElementById('d-jh-start').textContent = td(7);
    document.getElementById('d-jh-end').textContent   = td(8);
    document.getElementById('d-jh-desc').textContent  = td(9);
  }

  // Click vào Employee ID để mở panel chi tiết
  jhTable.addEventListener('click', (e) => {
    const link = e.target.closest('.jh-emp');
    if (!link) return;
    e.preventDefault();
    const tr = link.closest('tr');
    fillJHDetail(tr);
    jhDetail.classList.remove('hidden');
    jhDetail.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  // Xóa dòng
  jhTable.querySelectorAll('.jh-remove').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const tr = e.target.closest('tr');
      tr?.parentNode?.removeChild(tr);
      // TODO: show toast nếu bạn đã có hệ thống #toasts
    });
  });

  // Lọc dữ liệu
  document.getElementById('jh-apply-filter')?.addEventListener('click', () => {
    const emp   = (document.getElementById('jh-emp-id')?.value || '').trim().toLowerCase();
    const dept  = (document.getElementById('jh-dept')?.value || '').trim().toLowerCase();
    const pos   = (document.getElementById('jh-pos')?.value || '').trim().toLowerCase();
    const from  = document.getElementById('jh-from')?.value || '';
    const to    = document.getElementById('jh-to')?.value || '';

    const rows = jhTable.querySelectorAll('tbody tr');
    rows.forEach(row => {
      const rEmp   = row.children[2].textContent.trim().toLowerCase();
      const rDept  = row.children[6].textContent.trim().toLowerCase();
      const rPos   = row.children[5].textContent.trim().toLowerCase();
      const rStart = row.children[7].textContent.trim(); // YYYY-MM-DD
      const rEnd   = row.children[8].textContent.trim(); // YYYY-MM-DD

      const okEmp  = !emp  || rEmp.includes(emp);
      const okDept = !dept || rDept.includes(dept);
      const okPos  = !pos  || rPos.includes(pos);
      const okDate = (!from || rEnd >= from) && (!to || rStart <= to);

      row.style.display = (okEmp && okDept && okPos && okDate) ? '' : 'none';
    });
  });

  // Xóa hàng loạt (checkbox + nút Remove Selected)
  document.getElementById('jh-bulk-remove')?.addEventListener('click', () => {
    const rows = jhTable.querySelectorAll('tbody tr');
    rows.forEach(row => {
      const cb = row.querySelector('.row-check');
      if (cb?.checked) row.parentNode.removeChild(row);
    });
  });