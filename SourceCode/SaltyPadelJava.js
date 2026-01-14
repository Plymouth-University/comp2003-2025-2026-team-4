// Functions in charge of the (SPA) single page application
// Home page

function hide_other_pages() {
    var who_are_we = document.getElementById("who_we_are");
    var what_we_do = document.getElementById("what_we_do");
    var past_events = document.getElementById("past_events");
    var upcoming_events = document.getElementById("upcoming_events");
    var login_page = document.getElementById("login_page");
    var admin_page = document.getElementById("admin_page");
    who_are_we.style.display = "none";
    what_we_do.style.display = "none";
    past_events.style.display = "none";
    upcoming_events.style.display = "none";
    login_page.style.display = "none";
    admin_page.style.display = "none";
}

function hide_other_navs() {
    document.getElementById("main-nav").style.display = "none";
    document.getElementById("who-are-we-nav").style.display = "none";
    document.getElementById("what-we-do-nav").style.display = "none";
    document.getElementById("past-events-nav").style.display = "none";
    document.getElementById("upcoming-events-nav").style.display = "none";
    document.getElementById("admin-log-off-nav").style.display = "none";
}

function button_who_we_are() {
    // Variables transfer from HTML by getElementByID
    var home_page = document.getElementById("home_page");
    var who_are_we = document.getElementById("who_we_are");
    hide_other_pages();
    home_page.style.display = "none"; // hide home
    who_are_we.style.display = "block"; // show "who are we" page

    hide_other_navs();
    let nav = document.getElementById("who-are-we-nav");
    nav.style.display = "flex";
    nav.style.justifyContent = "center";
}

function button_what_we_do() {
    var home_page = document.getElementById("home_page");
    var what_we_do = document.getElementById("what_we_do");
    hide_other_pages();
    home_page.style.display = "none";
    what_we_do.style.display = "block";

    hide_other_navs();
    let nav = document.getElementById("what-we-do-nav");
    nav.style.display = "flex";
    nav.style.justifyContent = "center";
}

function button_past_events() {
    var home_page = document.getElementById("home_page");
    var past_events = document.getElementById("past_events");
    hide_other_pages();
    home_page.style.display = "none";
    past_events.style.display = "block";

    hide_other_navs();
    let nav = document.getElementById("past-events-nav");
    nav.style.display = "flex";
    nav.style.justifyContent = "center";
}

function button_upcoming_events() {
    var home_page = document.getElementById("home_page");
    var upcoming_events = document.getElementById("upcoming_events");
    hide_other_pages();
    home_page.style.display = "none";
    upcoming_events.style.display = "block";

    hide_other_navs();
    let nav = document.getElementById("upcoming-events-nav");
    nav.style.display = "flex";
    nav.style.justifyContent = "center";
}

function button_admin_login() {
    var home_page = document.getElementById("home_page");
    var login_page = document.getElementById("login_page");
    var login_button = document.getElementById("admin-login");
    login_button.style.display = "none";
    hide_other_pages();
    home_page.style.display = "none";
    login_page.style.display = "flex";
}

function admin_home() {
    var admin_page = document.getElementById("admin_page");
    hide_other_navs();
    let nav = document.getElementById("admin-log-off-nav");
    nav.style.display = "flex";
    nav.style.justifyContent = "center";
    hide_other_pages();
    admin_page.style.display = "block";
}

// Admin login page, add new events page, and edit home page
function button_home() {
    hide_other_pages();
    var login_button = document.getElementById("admin-login");
    login_button.style.display = "block";
    var home_page = document.getElementById("home_page");
    home_page.style.display = "block";

    hide_other_navs();
    document.getElementById("main-nav").style.display = "flex";
}

function button_manage_testimonials() {
    hide_other_pages();
}
function button_manage_past_events() {
    hide_other_pages();
}
function button_manage_upcoming_events() {
    hide_other_pages();
}
function button_manage_partners() {
    hide_other_pages();
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

// Ensures code code shows after page is loaded
document.addEventListener("DOMContentLoaded", () => {
    hide_other_pages();
    hide_other_navs();

    document.getElementById("home_page").style.display = "block";
    document.getElementById("main-nav").style.display = "flex";
})

//backend
function button_verify_login() {
    //login button calls this fn. if login fields are validated, go to admin home
    //init placeholder verification values
    let admin_user = "admin";
    let admin_password = "123";
    let username = document.getElementById("admin_username").value;
    let password = document.getElementById("admin_password").value;
    let warning1 = document.getElementById("warning1");
    let warning2 = document.getElementById("warning2");
    warning1.textContent = " ";
    warning2.textContent = " ";

    if (admin_user == username) {
        if (admin_password == password) {
            admin_home();
        } else { warning2.textContent = "Password invalid." }
    } else { warning1.textContent = "Enter a valid username" }
}

// Open WhatsApp group invite (replace the link with a real invite)
function button_join_our_whatsapp_group() {
    window.open('https://chat.whatsapp.com/your-invite-link', '_blank');
}

// On page load, check URL hash to open pages directly (e.g., #who)
window.addEventListener('DOMContentLoaded', function () {
    if (location.hash === '#who') {
        button_who_we_are();
    }
});

