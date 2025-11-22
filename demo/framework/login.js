// ========= Mock user store (DEMO) =========
const USERS = [
  { username: 'ceo',       password: '123456', role: 'CEO',      employeeId: 'E0001' },
  { username: 'hr.admin',  password: '123456', role: 'HR',       employeeId: 'E0002' },
  { username: 'manager',   password: '123456', role: 'Manager',  employeeId: 'E0100' },
  { username: 'staff01',   password: '123456', role: 'Employee', employeeId: 'E1001' },
  { username: 'staff02',   password: '123456', role: 'Employee', employeeId: 'E1002' },
];


//admin (CEO / HR / Manager)
const ADMIN_REDIRECT = '../demo/employee.php';
const ALLOWED_ROLES_ADMIN = new Set(['CEO', 'HR', 'Manager']);

//staff
const EMPLOYEE_REDIRECT = '../employee/index.php';
const ALLOWED_ROLES_EMPLOYEE = new Set(['Employee']);

function showError(msg) {
  const box = document.getElementById('error-box');
  box.textContent = msg;
  box.classList.remove('d-none');
}

function clearError() {
  const box = document.getElementById('error-box');
  box.textContent = '';
  box.classList.add('d-none');
}

function showToast(msg) {
  const host = document.getElementById('toasts'); 
  if (!host) return;
  const el = document.createElement('div');
  el.className = 'px-4 py-2 rounded-md shadow-sm bg-white border';
  el.textContent = msg;
  host.appendChild(el);
  setTimeout(() => el.remove(), 2200);
}

function findUser(username) {
  return USERS.find(u => u.username.toLowerCase() === username.toLowerCase());
}

function setSession(user, persist = false) {
  const payload = {
    username: user.username,
    role: user.role,
    employeeId: user.employeeId,
    loginAt: new Date().toISOString()
  };
  sessionStorage.setItem('auth_user', JSON.stringify(payload));
  if (persist) {
    localStorage.setItem('auth_user', JSON.stringify(payload));
  } else {
    localStorage.removeItem('auth_user'); // xoá bản cũ nếu không cần nhớ
  }
}


function getRedirectTarget(user) {
  const url = new URL(window.location.href);
  const redirectParam = url.searchParams.get('redirect');

  if (redirectParam) return redirectParam;

  if (ALLOWED_ROLES_ADMIN.has(user.role)) {
    return ADMIN_REDIRECT;
  }

  if (ALLOWED_ROLES_EMPLOYEE.has(user.role)) {
    return EMPLOYEE_REDIRECT;
  }
  return ADMIN_REDIRECT;
}


document.getElementById('toggle-pass')?.addEventListener('click', () => {
  const input = document.getElementById('password');
  const btn = document.getElementById('toggle-pass');
  const isPw = input.type === 'password';
  input.type = isPw ? 'text' : 'password';
  btn.textContent = isPw ? 'Hide' : 'Show';
});

document.getElementById('login-form')?.addEventListener('submit', (e) => {
  e.preventDefault();
  clearError();

  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;
  const remember = document.getElementById('rememberMe').checked;

  if (!username || !password) {
    showError('Please enter both username and password.');
    return;
  }

  const user = findUser(username);
  if (!user || user.password !== password) {
    showError('Incorrect username or password.');
    return;
  }

  setSession(user, remember);
  showToast('Signed in successfully');


  const target = getRedirectTarget(user);
  window.location.href = target;
});


(function restoreRemembered() {
  try {
    const saved = JSON.parse(localStorage.getItem('auth_user'));
    if (saved?.username) {
      const usernameInput = document.getElementById('username');
      const rememberCheckbox = document.getElementById('rememberMe');
      if (usernameInput) usernameInput.value = saved.username;
      if (rememberCheckbox) rememberCheckbox.checked = true;
    }
  } catch {
    // ignore
  }
})();
