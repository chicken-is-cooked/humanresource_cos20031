document.addEventListener("DOMContentLoaded", () => {
  const rows = Array.from(document.querySelectorAll(".salary-row"));
  const filterInput = document.getElementById("filter-emp-id");
  const filterBtn = document.getElementById("apply-filter");

  const detailSection = document.getElementById("salary-detail");
  const dId = document.getElementById("d-id");
  const dEmpId = document.getElementById("d-empid");
  const dHours = document.getElementById("d-hours");
  const dHourly = document.getElementById("d-hourly");
  const dBase = document.getElementById("d-base");
  const dBonus = document.getElementById("d-bonus");
  const dDeduction = document.getElementById("d-deduction");
  const dTotal = document.getElementById("d-total");

  function showDetailsFromRow(row) {
    if (!row) return;

    dId.textContent = row.dataset.id || "";
    dEmpId.textContent = row.dataset.empid || "";
    dHours.textContent = row.dataset.hours || "0";
    dHourly.textContent = row.dataset.hourly || "0";
    dBase.textContent = row.dataset.basesalary || "0";
    dBonus.textContent = row.dataset.bonus || "0";
    dDeduction.textContent = row.dataset.deduction || "0";
    dTotal.textContent = row.dataset.total || "0";

    detailSection.classList.remove("hidden");
  }

  rows.forEach((row) => {
    const checkbox = row.querySelector(".row-check");
    const viewBtn = row.querySelector(".view-details");

    if (checkbox) {
      checkbox.addEventListener("change", () => {
        // bỏ tick các dòng khác
        rows.forEach((r) => {
          const cb = r.querySelector(".row-check");
          if (cb && cb !== checkbox) cb.checked = false;
        });

        if (checkbox.checked) {
          showDetailsFromRow(row);
        } else {
          detailSection.classList.add("hidden");
        }
      });
    }

    if (viewBtn) {
      viewBtn.addEventListener("click", (e) => {
        e.preventDefault();
        if (checkbox) {
          checkbox.checked = true;
          rows.forEach((r) => {
            const cb = r.querySelector(".row-check");
            if (cb && cb !== checkbox) cb.checked = false;
          });
        }
        showDetailsFromRow(row);
      });
    }
  });

  function applyFilter() {
    const value = (filterInput.value || "").trim().toLowerCase();
    rows.forEach((row) => {
      const empId = (row.dataset.empid || "").toLowerCase();
      row.style.display = !value || empId.includes(value) ? "" : "none";
    });
  }

  if (filterBtn) {
    filterBtn.addEventListener("click", (e) => {
      e.preventDefault();
      applyFilter();
    });
  }

  if (filterInput) {
    filterInput.addEventListener("keyup", (e) => {
      if (e.key === "Enter") applyFilter();
    });
  }
});
