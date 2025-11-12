  document.querySelector('.sidebar-toggle')?.addEventListener('click', () => {
    document.getElementById('appSidebar')?.classList.toggle('open');
  });

  // Approve/Reject/Delete actions + simple filters
  (function () {
    const table = document.getElementById('leave-table');

    table.querySelectorAll('.btn-approve').forEach(btn => {
      btn.addEventListener('click', e => {
        const row = e.target.closest('tr');
        const badge = row.querySelector('.status-badge');
        badge.textContent = 'Approved';
        badge.className = 'inline-block px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 status-badge';
      });
    });

    table.querySelectorAll('.btn-reject').forEach(btn => {
      btn.addEventListener('click', e => {
        const row = e.target.closest('tr');
        const badge = row.querySelector('.status-badge');
        badge.textContent = 'Rejected';
        badge.className = 'inline-block px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 status-badge';
      });
    });

    table.querySelectorAll('.remove-row').forEach(btn => {
      btn.addEventListener('click', e => {
        const tr = e.target.closest('tr');
        tr?.parentNode?.removeChild(tr);
      });
    });

    // Filters
    document.getElementById('apply-filter')?.addEventListener('click', () => {
      const typeVal = document.getElementById('leave-type')?.value || '';
      const statusVal = document.getElementById('status')?.value || '';
      const from = document.getElementById('from-date')?.value || '';
      const to = document.getElementById('to-date')?.value || '';
      const empId = (document.getElementById('emp-id')?.value || '').trim();

      const rows = table.querySelectorAll('tbody tr');
      rows.forEach(row => {
        const rEmp  = row.children[1].textContent.trim();
        const rType = row.children[3].textContent.trim();
        const rStart = row.children[4].textContent.trim(); // YYYY-MM-DD
        const rEnd   = row.children[5].textContent.trim(); // YYYY-MM-DD
        const rStatus = row.querySelector('.status-badge')?.textContent.trim() || '';

        const inType = !typeVal || rType === typeVal;
        const inStatus = !statusVal || rStatus === statusVal;
        const inEmp = !empId || rEmp.includes(empId);
        const inDate =
          (!from || rEnd >= from) && // end after from
          (!to || rStart <= to);     // start before to

        row.style.display = (inType && inStatus && inEmp && inDate) ? '' : 'none';
      });
    });
  })();