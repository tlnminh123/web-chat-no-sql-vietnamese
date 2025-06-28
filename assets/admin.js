document.addEventListener('DOMContentLoaded', () => {
  // switch tab dọc
  document.querySelectorAll('.tab-links button').forEach(btn => {
    btn.onclick = () => {
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-links button').forEach(b => b.classList.remove('active'));
      document.getElementById(btn.dataset.tab).classList.add('active');
      btn.classList.add('active');
      loadTabData(btn.dataset.tab);
    };
  });

  // switch form-tab
  document.querySelectorAll('.form-tabs .form-tab-btn').forEach(btn => {
    btn.onclick = () => {
      document.querySelectorAll('.form-tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      loadForms(btn.dataset.form);
    };
  });

  loadTabData('users');
});

// Load tab tương ứng
function loadTabData(tab) {
  const ld = {
    users: loadUsers,
    forms: () => loadForms('unban'),
    register: () => {},
    userinfo: loadInfos
  };
  ld[tab] && ld[tab]();
}

// USERS
function loadUsers() {
  fetch('process.php?action=list_users')
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        document.getElementById('user-list').innerHTML = data.html;
        attachUserButtons();
      } else {
        document.getElementById('user-list').innerHTML = '⚠️ Lỗi tải dữ liệu';
      }
    });
}

function attachUserButtons() {
  document.querySelectorAll('[data-user-action]').forEach(btn => {
    btn.onclick = () => {
      const action = btn.dataset.userAction;
      const user = btn.dataset.username;
      fetch(`process.php?action=${action}&user=${encodeURIComponent(user)}`)
        .then(r => r.json())
        .then(obj => {
          if (obj.success) loadUsers();
          else if (obj.message) alert('Lỗi: ' + obj.message);
        });
    };
  });
}

// FORMS
function loadForms(type) {
  fetch(`process.php?action=list_forms&type=${type}`)
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        document.getElementById('form-list').innerHTML = data.html;
        document.getElementById('form-content').innerHTML = '';
        attachFormButtons();
      } else {
        document.getElementById('form-list').innerHTML = '<em>Lỗi khi tải form.</em>';
      }
    });
}

function attachFormButtons() {
  document.querySelectorAll('.btn-view').forEach(btn => {
    btn.onclick = () => {
      fetch(`process.php?action=view_form&path=${encodeURIComponent(btn.dataset.path)}`)
        .then(r => r.json())
        .then(data => {
          document.getElementById('form-content').innerHTML = data.success
            ? data.html
            : '<em>Không thể xem file.</em>';
        });
    };
  });

  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.onclick = () => {
      const path = btn.dataset.path;
      fetch(`process.php?action=delete_form&path=${encodeURIComponent(path)}`)
        .then(r => r.json())
        .then(data => {
          if (!data.success && data.message) {
            alert(data.message);
          }
          const activeTab = document.querySelector('.form-tab-btn.active').dataset.form;
          loadForms(activeTab);
        });
    };
  });
}

// REGISTER
document.getElementById('register').onsubmit = e => {
  e.preventDefault();
  const data = new FormData(e.target);
  fetch('process.php?action=register_user', {
    method: 'POST',
    body: data
  })
    .then(r => r.json())
    .then(res => {
      document.getElementById('register-msg').innerText = res.message || '';
      if (res.success) {
        e.target.reset();
        loadUsers();
      }
    });
};

// USER INFO
function loadInfos() {
  fetch('process.php?action=list_infos')
    .then(r => r.json())
    .then(data => {
      const container = document.getElementById('userinfo-list');
      if (data.success) {
        container.innerHTML = '<h3>Thông tin người dùng</h3>' + data.html;
      } else {
        container.innerHTML = '<h3>Thông tin người dùng</h3><em>Lỗi khi tải thông tin.</em>';
      }
    });
}
