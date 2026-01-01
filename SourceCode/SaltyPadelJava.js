// Functions in charge of the (SPA) single page application
// Home page
function button_who_we_are() {
    // Variables transfer from HTML by getElementByID
    var home_page = document.getElementById("home_page");
    var who_are_we = document.getElementById("who_we_are");
    home_page.style.display = "none"; // hide home
    who_are_we.style.display = "block"; // show "who are we" page
}

function button_what_we_do() {
    var home_page = document.getElementById("home_page");
    var what_we_do = document.getElementById("what_we_do");
    home_page.style.display = "none";
    what_we_do.style.display = "block";
}

function button_past_events() {
    var home_page = document.getElementById("home_page");
    var past_events = document.getElementById("past_events");
    home_page.style.display = "none";
    past_events.style.display = "block";
}

function button_upcoming_events() {
    var home_page = document.getElementById("home_page");
    var upcoming_events = document.getElementById("upcoming_events");
    home_page.style.display = "none";
    upcoming_events.style.display = "block";
}

function button_admin_login() {
    var login_page = document.getElementById("login_page");
    home_page.style.display = "none";
    login_page.style.display = "block";
}

function button_admin_home() {
    var home_page = document.getElementById("home_page");
    var admin_home = document.getElementById("admin_page");
    var login_page = document.getElementById("login_page");
    login_page.style.display = "none";
    home_page.style.display = "none";
    admin_home.style.display = "block";
}

// Admin login page, add new events page, and edit home page
function button_home() {
    var login_page = document.getElementById("login_page");
    var who_are_we = document.getElementById("who_we_are");
    var what_we_do = document.getElementById("what_we_do");
    var past_events = document.getElementById("past_events");
    var upcoming_events = document.getElementById("upcoming_events");
    var edit_home_page = document.getElementById("edit_home_page");
    var add_new_event = document.getElementById("add_new_event");
    var home_page = document.getElementById("home_page");
    var admin_page = document.getElementById("admin_page");
    login_page.style.display = "none";
    who_are_we.style.display = "none";
    what_we_do.style.display = "none";
    past_events.style.display = "none";
    upcoming_events.style.display = "none";
    edit_home_page.style.display = "none";
    add_new_event.style.display = "none";
    admin_page.style.display = "none";
    home_page.style.display = "block";
}

function button_edit_home() {
    var login_page = document.getElementById("login_page");
    var edit_home_page = document.getElementById("edit_home_page");
    login_page.style.display = "none";
    edit_home_page.style.display = "block";
}

function button_add_new_event() {
    var login_page = document.getElementById("login_page");
    var add_new_event = document.getElementById("add_new_event");
    login_page.style.display = "none";
    add_new_event.style.display = "block";
}

// What we do, who we are, past events, upcoming events
function button_contact() {
    var what_we_do = document.getElementById("what_we_do");
    var who_we_are = document.getElementById("who_we_are");
    var past_events = document.getElementById("past_events");
    var upcoming_events = document.getElementById("upcoming_events");
    what_we_do.style.display = "none";
    past_events.style.display = "none";
    upcoming_events.style.display = "none";
    who_we_are.style.display = "block";
}
