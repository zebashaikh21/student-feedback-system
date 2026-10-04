document.addEventListener("DOMContentLoaded", () => {
  // Show/hide role-specific registration fields.
  const role = document.getElementById("roleSelect");
  const studentFields = document.getElementById("studentFields");
  const facultyFields = document.getElementById("facultyFields");
  const adminFields = document.getElementById("adminFields");
  function updateRoleFields() {
    if (!role) return;
    studentFields.hidden = role.value !== "student";
    facultyFields.hidden = role.value !== "faculty";
    adminFields.hidden = role.value !== "admin";
    const studentId = studentFields.querySelector('[name="student_code"]');
    const semester = studentFields.querySelector('[name="semester"]');
    const facultyId = facultyFields.querySelector('[name="faculty_code"]');
    studentId.required = semester.required = role.value === "student";
    facultyId.required = role.value === "faculty";
  }
  if (role) { role.addEventListener("change", updateRoleFields); updateRoleFields(); }

  // Password visibility controls.
  document.querySelectorAll("[data-toggle-password]").forEach(btn => {
    btn.addEventListener("click", () => {
      const input = document.getElementById(btn.dataset.togglePassword);
      if (input) input.type = input.type === "password" ? "text" : "password";
    });
  });

  // Client-side password match check; PHP validates again on the server.
  const signup = document.getElementById("signupForm");
  if (signup) signup.addEventListener("submit", event => {
    const p = document.getElementById("signupPassword").value;
    const c = document.getElementById("confirmPassword").value;
    if (p !== c) { event.preventDefault(); alert("Passwords do not match."); }
  });

  // Confirm feedback submission and disable submit button to prevent double clicks.
  const feedback = document.getElementById("feedbackForm");
  if (feedback) feedback.addEventListener("submit", event => {
    if (!confirm("Submit your feedback? You cannot submit again for the same faculty and subject.")) {
      event.preventDefault(); return;
    }
    const button = feedback.querySelector('button[type="submit"]');
    if (button) { button.disabled = true; button.textContent = "Submitting..."; }
  });

  // Highlight selected rating options.
  document.querySelectorAll(".rating-group input").forEach(input => {
    const refresh = () => {
      document.querySelectorAll(`input[name="${CSS.escape(input.name)}"]`).forEach(r => r.closest("label").classList.toggle("selected", r.checked));
    };
    input.addEventListener("change", refresh); refresh();
  });

  // Auto-dismiss alerts.
  document.querySelectorAll(".alert").forEach(alert => {
    setTimeout(() => { alert.classList.add("fade-out"); setTimeout(() => alert.remove(), 450); }, 5000);
  });

  // Responsive sidebar toggle.
  const menu = document.getElementById("menuButton");
  const sidebar = document.querySelector(".sidebar");
  if (menu && sidebar) menu.addEventListener("click", () => sidebar.classList.toggle("open"));
  document.addEventListener("click", event => {
    if (sidebar && sidebar.classList.contains("open") && !sidebar.contains(event.target) && event.target !== menu) sidebar.classList.remove("open");
  });

  const print = document.getElementById("printReport");
  if (print) print.addEventListener("click", () => window.print());
});
