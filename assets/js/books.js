
// function setupAutocomplete definition
function setupAutocomplete(inputId, dropdownId, url) {

    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);

    if (!input || !dropdown) return;

    input.addEventListener("input", async function () {

        input.dataset.id = "";

        if (input.id === "authorSearch") {
            document.getElementById("authorId").value = "";
        }

        else if (input.id === "publisherSearch") {
            document.getElementById("publisherId").value = "";
        }

        else if (input.id === "categorySearch") {
            document.getElementById("categoryId").value = "";
        }

        const query = this.value.trim();

        if (query === "") {
            dropdown.innerHTML = "";
            dropdown.style.display = "none";
            return;
        }

        const response = await fetch(
            url + "?q=" + encodeURIComponent(query)
        );

        const results = await response.json();
        dropdown.style.display = "block";

        dropdown.innerHTML = "";
        selectedIndex = -1;

        if (results.length === 0) {

            dropdown.innerHTML = `
                <div class="dropdown-item text-muted">No author found</div>
                <div class="dropdown-item text-primary add-author">
                    <i class="bi bi-plus-circle"></i> Add New Author
                </div>
            `;

            return;
        }

        results.forEach(result => {

            const id = Object.values(result)[0];
            const name = Object.values(result)[1];

            dropdown.innerHTML += `
                <div class="dropdown-item autocomplete-item"
                data-id="${id}">
                ${name}
                </div>
            `;

        });

    });

    input.addEventListener("blur", function () {

        setTimeout(() => {

            if (
                input.value.trim() !== "" && input.dataset.id === "") {

                // show your validation here later
                console.log("Invalid selection");

            }

        }, 150);

    });

    input.addEventListener("keydown", function (e) {

        const items = dropdown.querySelectorAll(".autocomplete-item");

        if (items.length === 0) return;

        if (e.key === "ArrowDown") {

            e.preventDefault();

            selectedIndex++;

            if (selectedIndex >= items.length) {
                selectedIndex = 0;
            }

        }

        else if (e.key === "ArrowUp") {

            e.preventDefault();

            selectedIndex--;

            if (selectedIndex < 0) {
                selectedIndex = items.length - 1;
            }

        }

        else if (e.key === "Enter") {

            if (selectedIndex >= 0) {

                e.preventDefault();

                items[selectedIndex].click();

            }

        }

        items.forEach(item => item.classList.remove("active"));

        if (selectedIndex >= 0) {
            items[selectedIndex].classList.add("active");
            items[selectedIndex].scrollIntoView({
                block: "nearest"
            });
        }

    });

    dropdown.addEventListener("click", function (e) {

        const item = e.target.closest(".autocomplete-item");

        if (!item) return;

        input.value = item.textContent.trim();
        input.dataset.id = item.dataset.id;

        if (input.id === "authorSearch") {
            document.getElementById("authorId").value = item.dataset.id;
        }

        else if (input.id === "publisherSearch") {
            document.getElementById("publisherId").value = item.dataset.id;
        }

        else if (input.id === "categorySearch") {
            document.getElementById("categoryId").value = item.dataset.id;
        }

        dropdown.innerHTML = "";

    });

    document.addEventListener("click", function (e) {

        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.innerHTML = "";
        }

    });

}

// function setupAutocomplete calls:
// Author
setupAutocomplete(
    "authorSearch",
    "authorDropdown",
    "../assets/ajax/search_author.php"
);

// Publisher
setupAutocomplete(
    "publisherSearch",
    "publisherDropdown",
    "../assets/ajax/search_publisher.php"
);

// Category
setupAutocomplete(
    "categorySearch",
    "categoryDropdown",
    "../assets/ajax/search_category.php"
);

document.querySelectorAll(".edit-book").forEach(button => {

    button.addEventListener("click", async function () {

        const id = this.dataset.id;

        const response = await fetch(
            "../assets/ajax/get_book.php?id=" + id
        );

        const book = await response.json();

        document.getElementById("title").value = book.title;
        document.getElementById("isbn").value = book.isbn;
        document.getElementById("publicationYear").value = book.publication_year;

        document.getElementById("total").value = book.total;
        document.getElementById("available").value = book.available;

        document.getElementById("description").value = book.description;

        // Author
        document.getElementById("authorSearch").value = book.author_name;
        document.getElementById("authorSearch").dataset.id = book.author_id;
        document.getElementById("authorId").value = book.author_id;

        // Publisher
        document.getElementById("publisherSearch").value = book.publisher_name;
        document.getElementById("publisherSearch").dataset.id = book.publisher_id;
        document.getElementById("publisherId").value = book.publisher_id;

        // Category
        document.getElementById("categorySearch").value = book.category_name;
        document.getElementById("categorySearch").dataset.id = book.category_id;
        document.getElementById("categoryId").value = book.category_id;

        document.getElementById("bookId").value = book.book_id;
        document.getElementById("bookForm").action = "update_book.php";

        document.getElementById("modalTitle").textContent = "Edit Book";

        document.getElementById("saveBookBtn").innerHTML = `
            <i class="bi bi-pencil-square"></i>
            Update Book
        `;

    });

});

document.getElementById("addBookBtn").addEventListener("click", function () {

    document.getElementById("bookForm").reset();
    document.getElementById("bookForm").action = "save_book.php";

    document.getElementById("bookId").value = "";

    document.getElementById("authorId").value = "";
    document.getElementById("publisherId").value = "";
    document.getElementById("categoryId").value = "";

    document.getElementById("authorSearch").value = "";
    document.getElementById("publisherSearch").value = "";
    document.getElementById("categorySearch").value = "";

    document.getElementById("authorSearch").dataset.id = "";
    document.getElementById("publisherSearch").dataset.id = "";
    document.getElementById("categorySearch").dataset.id = "";

    document.getElementById("modalTitle").textContent = "Add New Book";

    document.getElementById("saveBookBtn").innerHTML = `
        <i class="bi bi-floppy"></i>
        Save Book
    `;

});

document.querySelectorAll(".delete-book").forEach(button => {

    button.addEventListener("click", function () {

        const id = this.dataset.id;
        const title = this.dataset.title;

        if (confirm(`Delete "${title}" ?`)) {

            window.location.href =
                "delete_book.php?id=" + id;

        }

    });

});