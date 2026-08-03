document.querySelectorAll(".return-book").forEach(button => {

    button.addEventListener("click", function () {

        if (!confirm("Return this book?")) {
            return;
        }

        window.location.href =
            "return.php?id=" + this.dataset.id;

    });

});