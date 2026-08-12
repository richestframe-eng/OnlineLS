document.addEventListener("DOMContentLoaded", function () {

    const currentPage = window.location.pathname.split("/").pop();

    const sidebarLinks = document.querySelectorAll(".sidebar-menu li a");

    sidebarLinks.forEach(function (link) {

        const linkPage = link.getAttribute("href").split("/").pop();

        if (linkPage === currentPage) {
            link.parentElement.classList.add("active");
        }

    });

});