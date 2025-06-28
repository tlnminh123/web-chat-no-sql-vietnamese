function toggleDropdown() {
  const dropdown = document.getElementById("userDropdown");
  dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

window.addEventListener("click", function (event) {
  const trigger = document.querySelector(".user-dropdown");
  const dropdown = document.getElementById("userDropdown");
  if (!trigger.contains(event.target)) dropdown.style.display = "none";
});

function copyCode(button) {
  const code = button.nextElementSibling.querySelector("code");
  navigator.clipboard.writeText(code.innerText).then(() => {
    button.textContent = "✅ Copied";
    setTimeout(() => (button.textContent = "📋 Copy"), 2000);
  }).catch(() => {
    button.textContent = "❌ Lỗi";
  });
}

$(document).ready(function () {
  const messages = $('#messages');
  const textarea = $('textarea[name="message"]');

  // ====== GỬI TIN NHẮN ======
  $('.message-form').submit(function (e) {
    e.preventDefault();
    let message = textarea.val();
    if (message.trim() === "") return;

    $.post("process.php", {
      action: "send",
      message: message
    }, function (res) {
      if (res.status === "ok") {
        textarea.val('');
        loadMessages(true); // có scroll xuống
      }
    }, "json");
  });

  // ====== GỬI BẰNG ENTER ======
  textarea.on("keydown", function (e) {
    if (e.key === "Enter") {
      if (e.shiftKey) return; // Cho xuống dòng
      e.preventDefault();
      $('.message-form').submit();
    }
  });

  // ====== LOAD TIN NHẮN ======
  function loadMessages(autoScroll = false) {
    $.get("process.php", { action: "fetch" }, function (res) {
      if (res.status === "ok") {
        let isAtBottom = Math.abs(messages[0].scrollHeight - messages.scrollTop() - messages.outerHeight()) < 100;

        let html = "";
        res.messages.forEach((m, i) => {
          let content = m.deleted === "yes"
            ? "<i>(đã thu hồi)</i>"
            : highlightMessage(m.text);

          html += `
            <div class='message'>
              <b>${escapeHTML(m.user)}:</b> ${content}
              <i>(${escapeHTML(m.time)})</i>
              ${m.canRevoke ? `<button class="revoke-btn" data-id="${i}">↩️ Thu hồi</button>` : ""}
            </div>
          `;
        });

        messages.html(html);

        if (autoScroll || isAtBottom) {
          messages.scrollTop(messages[0].scrollHeight);
        }
      }
    }, "json");
  }

  // ====== THU HỒI/XÓA TIN NHẮN ======
  $(document).on('click', '.revoke-btn', function () {
    const id = $(this).data('id');
    if (!confirm("Bạn có chắc chắn muốn thu hồi tin nhắn này?")) return;

    $.post("process.php", {
      action: "revoke",
      id: id
    }, function (res) {
      if (res.status === "ok") {
        loadMessages();
      }
    }, "json");
  });

  // ====== CHECK BỊ BAN ======
  function checkBan() {
    $.get("process.php", { action: "checkban" }, function (res) {
      if (res.status === "banned") {
        alert("⚠️ Tài khoản của bạn đã bị ban!");
        window.location.href = "/logout.php";
      }
    }, "json");
  }

  // ====== Escape & Highlight code ======
  function escapeHTML(text) {
    return $('<div>').text(text).html();
  }

  function highlightMessage(msg) {
    const html = escapeHTML(msg);
    if (/(\<\?php|int main\(|#include|std::|cout|cin)/.test(msg)) {
      return `<div class='code-block'><button class='copy-btn' onclick='copyCode(this)'>📋 Copy</button><pre class='cpp-code'><code>${html}</code></pre></div>`;
    }
    if (/(\bdef\b|\bimport\b|\bprint\()/i.test(msg)) {
      return `<div class='code-block'><button class='copy-btn' onclick='copyCode(this)'>📋 Copy</button><pre class='python-code'><code>${html}</code></pre></div>`;
    }
    return html.replace(/\n/g, "<br>");
  }

  // ====== KHỞI ĐỘNG ======
  loadMessages(true);
  setInterval(() => loadMessages(false), 2000);  // cập nhật tin nhắn
  setInterval(checkBan, 5000);                   // kiểm tra bị ban
});
