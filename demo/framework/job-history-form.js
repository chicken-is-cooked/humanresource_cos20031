// ========= Constants =========
const JH_KEY = 'job_history_v1';
const JH_REDIRECT = '../demo/job-history.html';

// ========= Storage Functions =========
function getJH() {
    try {
        return JSON.parse(localStorage.getItem(JH_KEY)) || [];
    } catch {
        return [];
    }
}

function setJH(list) {
    localStorage.setItem(JH_KEY, JSON.stringify(list));
}

function nextJHId(list) {
    // Find max number from IDs like JH-001
    let max = 0;
    for (const r of list) {
        const n = parseInt(String(r.id || '').replace(/\D/g, ''), 10);
        if (!isNaN(n) && n > max) max = n;
    }
    return 'JH-' + String(max + 1).padStart(3, '0');
}

// ========= UI Helper Functions =========
function showToast(msg) {
    const host = document.getElementById('toasts');
    if (!host) return;
    
    const el = document.createElement('div');
    el.className = 'px-4 py-2 rounded-md shadow-sm bg-white border';
    el.textContent = msg;
    host.appendChild(el);
    setTimeout(() => el.remove(), 2200);
}

function toggleSidebar() {
    const el = document.getElementById('sidebar');
    el?.classList.toggle('-translate-x-full');
}

// ========= Form Submission =========
document.getElementById('jh-form')?.addEventListener('submit', (e) => {
    e.preventDefault();

    // Get form values
    const empId = document.getElementById('jh-emp-id').value.trim();
    const first = document.getElementById('jh-first').value.trim();
    const last = document.getElementById('jh-last').value.trim();
    const posId = document.getElementById('jh-pos-id').value.trim();
    const depId = document.getElementById('jh-dept-id').value.trim();
    const start = document.getElementById('jh-start').value;
    const end = document.getElementById('jh-end').value;
    const desc = document.getElementById('jh-desc').value.trim();

    // Validation: end date >= start date
    if (start && end && end < start) {
        showToast('⚠️ End Date must be after Start Date');
        return;
    }

    // Create and save record
    const list = getJH();
    const id = nextJHId(list);

    const record = {
        id,
        employeeId: empId,
        firstName: first,
        lastName: last,
        positionId: posId,
        departmentId: depId,
        startDate: start,
        endDate: end || '',
        description: desc
    };

    list.push(record);
    setJH(list);

    // Redirect to list page
    window.location.href = JH_REDIRECT;
});

// ========= Event Listeners =========
document.getElementById('sidebar-toggle')?.addEventListener('click', toggleSidebar);