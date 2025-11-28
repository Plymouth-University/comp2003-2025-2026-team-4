// Functions in charge of the (SPA) single page application

function button_who_are_we() {
    var home_page = document.getElementById("home_page");
    var who_are_we = document.getElementById("who_are_we");
    home_page.style.display = "none"; // hide home
    who_are_we.style.display = "block"; // show "who are we" page
}