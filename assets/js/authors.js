// ===============================
// Add Author Button
// ===============================

document.getElementById("addAuthorBtn").addEventListener("click", function () {

    document.getElementById("authorForm").reset();

    document.getElementById("authorForm").action = "save_author.php";

    document.getElementById("authorId").value = "";

    document.getElementById("modalTitle").textContent = "Add New Author";

});

// ===============================
// Edit Author
// ===============================

document.querySelectorAll(".edit-author").forEach(button => {

    button.addEventListener("click", function () {

        const id = this.dataset.id;

        fetch("get_author.php?id=" + id)

        .then(response => response.json())

        .then(data => {

            document.getElementById("authorId").value = data.author_id;

            document.getElementById("authorName").value = data.author_name;

            document.getElementById("authorForm").action = "update_author.php";

            document.getElementById("modalTitle").textContent = "Update Author";
            document.getElementById("saveAuthorBtn").innerHTML = `
                <i class="bi bi-pencil-square"></i>
                Update Author
            `;

            new bootstrap.Modal(document.getElementById("addAuthorModal")).show();

        });

    });

});

// ===============================
// Delete Author
// ===============================

document.querySelectorAll(".delete-author").forEach(button => {

    button.addEventListener("click", function () {

        const id = this.dataset.id;
        const name = this.dataset.name;

        if (confirm(`Are you sure you want to delete "${name}"?`)) {

            window.location.href = "delete_author.php?id=" + id;

        }

    });

});