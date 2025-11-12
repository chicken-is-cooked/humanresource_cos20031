 // ==== CONFIG ====
  const ATTENDANCE_KEY  = 'attendance_records_v1';
  const ATTENDANCE_PAGE = '../demo/attendance.html'; // đổi đường dẫn nếu khác

  function getAttendance() {
    try { return JSON.parse(localStorage.getItem(ATTENDANCE_KEY)) || []; }
    catch { return []; }
  }
  function setAttendance(list) {
    localStorage.setItem(ATTENDANCE_KEY, JSON.stringify(list));
  }
  function calcMinutesWorked(dateStr, timeInStr, timeOutStr) {
    const start = new Date(`${dateStr}T${timeInStr}`);
    const end   = new Date(`${dateStr}T${timeOutStr}`);
    const diff  = Math.max(0, (end - start) / (1000 * 60));
    return Math.round(diff);
  }
  function nextId(list) {
    const last = list.at(-1);
    const n = last ? (parseInt(String(last.id).replace(/\D/g,''), 10) || 0) + 1 : 1;
    return 'A-' + String(n).padStart(3,'0');
  }

  // Optional: toast mini
  function showToast(msg) {
    const host = document.getElementById('toasts');
    if (!host) return;
    const el = document.createElement('div');
    el.className = 'px-4 py-2 rounded-md shadow-sm bg-white border';
    el.textContent = msg;
    host.appendChild(el);
    setTimeout(() => el.remove(), 2200);
  }

  document.getElementById('attendance-form')?.addEventListener('submit', (e) => {
    e.preventDefault();

    const empId   = document.getElementById('employee-id').value.trim();
    const date    = document.getElementById('date').value;
    const timeIn  = document.getElementById('time-in').value;
    const timeOut = document.getElementById('time-out').value;
    const status  = document.getElementById('status').value;
    const note    = document.getElementById('note')?.value?.trim() || '';

    // Basic validation: timeOut > timeIn
    if (timeIn && timeOut) {
      const s = new Date(`${date}T${timeIn}`), e2 = new Date(`${date}T${timeOut}`);
      if (e2 <= s) {
        showToast('⚠️ Time Out must be after Time In');
        return;
      }
    }

    const minutes = calcMinutesWorked(date, timeIn, timeOut);
    const list = getAttendance();

    const record = {
      id: nextId(list),
      employeeId: empId,
      firstName: '',      
      lastName:  '',
      workDate:  date,
      timeIn, timeOut,
      minutesWorked: minutes,
      description: note || (status === 'late' ? 'Late' : (status === 'absent' ? 'Absent' : 'Present')),
      status
    };

    list.push(record);
    setAttendance(list);

    window.location.href = ATTENDANCE_PAGE;
  });

  document.querySelector('.sidebar-toggle')?.addEventListener('click', () => {
    document.getElementById('appSidebar')?.classList.toggle('open');
  });