function selectRole(role) {
    document.getElementById("roleInput").value = role;

    let studentCard = document.getElementById("studentCard");
    let adminCard = document.getElementById("adminCard");

    studentCard.classList.remove("active");
    adminCard.classList.remove("active");

    if (role === "student") {
        studentCard.classList.add("active");
    } else {
        adminCard.classList.add("active");
    }
}