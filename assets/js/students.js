
const photoContainer = document.getElementById("photoContainer");
const studentPhoto = document.getElementById("studentPhoto");
const photoPreview = document.getElementById("photoPreview");
const photoIcon = document.getElementById("photoIcon");

document.getElementById("studentPhoto").addEventListener("change", function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();

    reader.onload = function (e) {
        document.getElementById("photoPreview").src = e.target.result;
    };

    reader.readAsDataURL(file);
});

photoContainer.addEventListener("click", function () {
    studentPhoto.click();
});

studentPhoto.addEventListener("change", function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();

    reader.onload = function (e) {
        photoPreview.src = e.target.result;
        photoPreview.classList.remove("d-none");
        photoIcon.classList.add("d-none");
    };

    reader.readAsDataURL(file);
});

document.querySelectorAll(".edit-student").forEach(button => {

    button.addEventListener("click", async function () {

        const id = this.dataset.id;

        const response = await fetch(
            "../assets/ajax/get_std.php?id=" + id
        );

        const student = await response.json();

        document.getElementById("studentId").value = student.student_id;

        document.getElementById("fullName").value = student.full_name;
        document.getElementById("email").value = student.email;
        document.getElementById("phone").value = student.phone;
        document.getElementById("address").value = student.address;

        // Password fields stay empty
        document.getElementById("password").value = "";
        document.getElementById("confirmPassword").value = "";

        // Preview current photo
        document.getElementById("photoPreview").src =
            "../assets/uploads/" + student.photo;

        document.getElementById("photoPreview").classList.remove("d-none");
        document.getElementById("photoIcon").classList.add("d-none");

        // Change form action
        document.getElementById("studentForm").action = "update_std.php";

        // Modal title
        document.getElementById("modalTitle").textContent = "Edit Student";

        // Button text
        document.getElementById("saveStdBtn").innerHTML =
            '<i class="bi bi-pencil-square"></i> Update Student';

        // Open modal
        new bootstrap.Modal(
            document.getElementById("addStdModal")
        ).show();

    });

});

document.getElementById("addStdBtn").addEventListener("click", function () {

    document.getElementById("studentForm").reset();
    document.getElementById("studentForm").action = "save_std.php";

    document.getElementById("studentId").value = "";

    document.getElementById("modalTitle").textContent = "Add New Student";

    document.getElementById("saveStdBtn").innerHTML = `
        <i class="bi bi-floppy"></i>
        Save Student
    `;

    document.getElementById("studentPhoto").value = "";

    document.getElementById("photoPreview").src = "";
    document.getElementById("photoPreview").classList.add("d-none");

    document.getElementById("photoIcon").classList.remove("d-none");

});

document.querySelectorAll(".delete-std").forEach(button => {

    button.addEventListener("click", function () {
        if (!confirm("Are you sure you want to delete this student?")) {
            return;
        }

        window.location.href = "delete_std.php?id=" + this.dataset.id;
    });
});