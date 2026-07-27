const input = document.getElementById("authorSearch");
const dropdown = document.getElementById("authorDropdown");

input.addEventListener("input", async function () {

    const query = this.value.trim();

    if (query === "") return;

    const response = await fetch(
        "../assets/ajax/search_author.php?q=" + encodeURIComponent(query)
    );

    const authors = await response.json();

    authorDropdown.innerHTML = "";

    authors.forEach(function (author) {

        authorDropdown.innerHTML += `
        <div class="dropdown-item author-item"
             data-id="${author.author_id}">
            ${author.author_name}
        </div>
    `;

    });

});

dropdown.addEventListener("click", function (e) {

    const item = e.target.closest(".author-item");

    if (!item) return;

    input.value = item.textContent.trim();

    input.dataset.id = item.dataset.id;

    dropdown.innerHTML = "";

});