document.addEventListener("DOMContentLoaded", () => {
    const deleteForm = document.getElementById("deleteAccountForm");

    if (deleteForm) {
        deleteForm.addEventListener("submit", (e) => {
            const confirmed = confirm("Are you sure you want to delete your account? This action cannot be undone.");

            if (!confirmed) {
                e.preventDefault();
            }
        });
    }
});