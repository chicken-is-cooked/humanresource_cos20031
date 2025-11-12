// === Sidebar mobile toggle ===
const sidebar = document.getElementById('sidebar');
document.getElementById('sidebar-toggle').addEventListener('click', () => {
  sidebar.classList.toggle('-translate-x-full');
});

// === Helpers ===
const $ = (s) => document.querySelector(s);
const $$ = (s) => Array.from(document.querySelectorAll(s));
function toast(msg, ok = true) {
  const el = document.createElement('div');
  el.className = `rounded-lg px-4 py-2 text-white ${ok ? 'bg-green-600' : 'bg-red-600'}`;
  el.textContent = msg;
  $('#toasts').appendChild(el);
  setTimeout(() => el.remove(), 2200);
}

// === Stats based on table rows ===
function recomputeStats() {
  const rows = $$('#employee-table tbody tr');
  $('#total-employees').textContent = rows.length;

  // Count unique departments
  const deptSet = new Set(rows.map(tr => tr.children[11]?.textContent.trim()).filter(Boolean));
  $('#total-departments').textContent = deptSet.size;
  
  // Set active employees (all for now since we don't have a status field)
  $('#active-employees').textContent = rows.length;
  
  // Set average salary (placeholder since we don't have salary data)
  $('#avg-salary').textContent = '$0';
}

// === Delete (per row) ===
$('#employee-table tbody').addEventListener('click', (e) => {
  if (!e.target.classList.contains('remove-row')) return;
  const tr = e.target.closest('tr');
  tr?.remove();
  recomputeStats();
  toast('Employee removed successfully');
});

// === Remove Selected ===
$('#remove-employee').addEventListener('click', () => {
  const selected = $$('#employee-table .row-check:checked').map(c => c.closest('tr'));
  if (!selected.length) return toast('No employees selected', false);
  selected.forEach(tr => tr.remove());
  $('#check-all').checked = false;
  recomputeStats();
  toast(`Removed ${selected.length} employee(s)`);
});

// === Check-all ===
// Add check-all checkbox to table header
const checkAllCell = document.createElement('input');
checkAllCell.type = 'checkbox';
checkAllCell.id = 'check-all';
$('#employee-table thead tr td').appendChild(checkAllCell);

$('#check-all').addEventListener('change', (e) => {
  $$('#employee-table .row-check').forEach(c => c.checked = e.target.checked);
});

// === First render ===
recomputeStats();

// === Sidebar Navigation ===
$$('.sidebar-item').forEach(item => {
  item.addEventListener('click', () => {
    $$('.sidebar-item').forEach(i => i.classList.remove('active'));
    item.classList.add('active');
  });
});