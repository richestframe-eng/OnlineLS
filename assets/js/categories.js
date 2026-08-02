// ===============================
// Add Category Button
// ===============================

document.getElementById("addCategoryBtn").addEventListener("click", function () {

    document.getElementById("categoryForm").reset();

    document.getElementById("categoryForm").action = "save_category.php";

    document.getElementById("categoryId").value = "";

    document.getElementById("modalTitle").textContent = "Add New Category";

});

// ===============================
// Edit Category
// ===============================

document.querySelectorAll(".edit-category").forEach(button => {

    button.addEventListener("click", function () {

        const id = this.dataset.id;

        fetch("get_category.php?id=" + id)

        .then(response => response.json())

        .then(data => {

            document.getElementById("categoryId").value = data.category_id;

            document.getElementById("categoryName").value = data.category_name;

            document.getElementById("categoryForm").action = "update_category.php";

            document.getElementById("modalTitle").textContent = "Update Category";
            document.getElementById("saveCategoryBtn").innerHTML = `
                <i class="bi bi-pencil-square"></i>
                Update Category
            `;

            new bootstrap.Modal(document.getElementById("addCategoryModal")).show();

        });

    });

});

// ===============================
// Delete Category
// ===============================

document.querySelectorAll(".delete-category").forEach(button => {

    button.addEventListener("click", function () {

        const id = this.dataset.id;
        const name = this.dataset.name;

        if (confirm(`Are you sure you want to delete "${name}"?`)) {

            window.location.href = "delete_category.php?id=" + id;

        }

    });

});