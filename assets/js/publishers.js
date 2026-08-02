// ===============================
// Add Publisher Button
// ===============================

document.getElementById("addPublisherBtn").addEventListener("click", function () {

    document.getElementById("publisherForm").reset();

    document.getElementById("publisherForm").action = "save_publisher.php";

    document.getElementById("publisherId").value = "";

    document.getElementById("modalTitle").textContent = "Add New Publisher";

});

// ===============================
// Edit Publisher
// ===============================

document.querySelectorAll(".edit-publisher").forEach(button => {

    button.addEventListener("click", function () {

        const id = this.dataset.id;

        fetch("get_publisher.php?id=" + id)

        .then(response => response.json())

        .then(data => {

            document.getElementById("publisherId").value = data.publisher_id;

            document.getElementById("publisherName").value = data.publisher_name;

            document.getElementById("publisherForm").action = "update_publisher.php";

            document.getElementById("modalTitle").textContent = "Update Publisher";
            document.getElementById("savePublisherBtn").innerHTML = `
                <i class="bi bi-pencil-square"></i>
                Update Publisher
            `;

            new bootstrap.Modal(document.getElementById("addPublisherModal")).show();

        });

    });

});

// ===============================
// Delete Publisher
// ===============================

document.querySelectorAll(".delete-publisher").forEach(button => {

    button.addEventListener("click", function () {

        const id = this.dataset.id;
        const name = this.dataset.name;

        if (confirm(`Are you sure you want to delete "${name}"?`)) {

            window.location.href = "delete_publisher.php?id=" + id;

        }

    });

});