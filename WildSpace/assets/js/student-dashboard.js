document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("reservationModal");
    const closeModal = document.getElementById("closeModal");
    const message = document.querySelector(".message");

    if (message) {
        setTimeout(() => {
            message.style.display = "none";
        }, 3000);
    }

    document.querySelectorAll("button.calendar-reservation").forEach((pill) => {
        pill.addEventListener("click", () => {
            document.getElementById("modalStudentName").textContent = pill.dataset.studentName;
            document.getElementById("modalReservationId").textContent = pill.dataset.id;
            document.getElementById("modalDate").textContent = pill.dataset.date;
            document.getElementById("modalCapacity").textContent = pill.dataset.capacity;
            document.getElementById("modalSpaceType").textContent = pill.dataset.spaceType;
            document.getElementById("modalStatus").textContent = pill.dataset.status;
            document.getElementById("modalApprovedBy").textContent = pill.dataset.approvedBy;

            modal.classList.add("active");
        });
    });

    closeModal.addEventListener("click", () => {
        modal.classList.remove("active");
    });

    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.remove("active");
        }
    });
});

const capacityInput = document.getElementById("capacityInput");
const spaceTypeSelect = document.getElementById("spaceTypeSelect");
const soloTableOption = document.getElementById("soloTableOption");

function updateSoloTableOption() {
    const seats = parseInt(capacityInput.value, 10);

    if (seats > 1) {
        soloTableOption.disabled = true;

        if (spaceTypeSelect.value === "Solo Table") {
            spaceTypeSelect.value = "";
        }
    } else {
        soloTableOption.disabled = false;
    }
}

if (capacityInput && spaceTypeSelect && soloTableOption) {
    capacityInput.addEventListener("input", updateSoloTableOption);
    capacityInput.addEventListener("change", updateSoloTableOption);

    updateSoloTableOption();
}
