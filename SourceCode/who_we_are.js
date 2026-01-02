function button_home() {
    var login_page = document.getElementById("login_page");
    var who_are_we = document.getElementById("who_we_are");
    var what_we_do = document.getElementById("what_we_do");
    var past_events = document.getElementById("past_events");
    var upcoming_events = document.getElementById("upcoming_events");
    var edit_home_page = document.getElementById("edit_home_page");
    var add_new_event = document.getElementById("add_new_event");
    var home_page = document.getElementById("home_page");
    login_page.style.display = "none";
    who_are_we.style.display = "none";
    what_we_do.style.display = "none";
    past_events.style.display = "none";
    upcoming_events.style.display = "none";
    edit_home_page.style.display = "none";
    add_new_event.style.display = "none";
    home_page.style.display = "block";
}
function button_contact() {
    window.location.href = "contact_us:.html";
}

function button_join_our_whatsapp_group() {
    window.location.href = "contact_us.html";
}
