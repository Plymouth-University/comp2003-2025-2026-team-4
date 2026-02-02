// ========================================
// CORE PAGE SWITCHING FUNCTIONS
// ========================================

/* =========================================================
GLOBAL STATE
   ========================================================= */
let has_unsaved_changes = false;
let pending_navigation_action = null;
let pending_external_url = null;

/* =========================================================
HELPERS
   ========================================================= */
function mark_unsaved_changes() {
    has_unsaved_changes = true;
}

function clear_unsaved_changes() {
    has_unsaved_changes = false;
}

function safe_hide(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = "none";
}

function safe_show(id, display_value = "block") {
    const el = document.getElementById(id);
    if (el) el.style.display = display_value;
}

function hide_other_pages() {
    document.getElementById("who_we_are").style.display = "none";
    document.getElementById("what_we_do").style.display = "none";
    document.getElementById("past_events").style.display = "none";
    document.getElementById("upcoming_events").style.display = "none";
    document.getElementById("login_page").style.display = "none";
    document.getElementById("admin_page").style.display = "none";
    document.getElementById("manage_testimonials").style.display = "none";
    document.getElementById("add_new_event").style.display = "none";
    // NOTE: home_page is controlled by button_home / other buttons
}

function hide_other_navs() {
    document.getElementById("main-nav").style.display = "none";
    document.getElementById("who-are-we-nav").style.display = "none";
    document.getElementById("what-we-do-nav").style.display = "none";
    document.getElementById("past-events-nav").style.display = "none";
    document.getElementById("upcoming-events-nav").style.display = "none";
    document.getElementById("admin-nav").style.display = "none";
}

/* =========================================================
UNSAVED CHANGES NAVIGATION GUARD
Use: try_navigate(() => button_home());
   ========================================================= */
function try_navigate(action_fn) {
    if (has_unsaved_changes) {
    pending_navigation_action = action_fn;
    open_modal("modal-unsaved-changes");
    return;
}
action_fn();
}

function setup_unsaved_modal_buttons() {
    const stay = document.getElementById("unsaved-stay-btn");
    const leave = document.getElementById("unsaved-leave-btn");
    if (stay) {
    stay.onclick = function (event) {
        if (event) event.preventDefault();
        pending_navigation_action = null;
        close_modal("modal-unsaved-changes");
    };
}
if (leave) {
    leave.onclick = function (event) {
        if (event) event.preventDefault();
        close_modal("modal-unsaved-changes");
        has_unsaved_changes = false;
        if (typeof pending_navigation_action === "function") {
        pending_navigation_action();}
        pending_navigation_action = null;
    };
}
}


function button_who_we_are() {
    var home_page = document.getElementById("home_page");
    var who_are_we = document.getElementById("who_we_are");
    hide_other_pages();
    home_page.style.display = "none";
    who_are_we.style.display = "block";

    hide_other_navs();
    let nav = document.getElementById("who-are-we-nav");
    nav.style.display = "flex";
    nav.style.justifyContent = "center";
    
    // Update mobile navigation
    update_mobile_nav('who_we_are');
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
    
    // Update mobile navigation
    update_mobile_nav('what_we_do');
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
    
    // Update mobile navigation
    update_mobile_nav('past_events');
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
    
    // Update mobile navigation
    update_mobile_nav('upcoming_events');
}

// ========================================
// ADMIN FUNCTIONS
// ========================================

function button_admin_login() {
    var home_page = document.getElementById("home_page");
    var login_page = document.getElementById("login_page");
    var login_button = document.getElementById("admin-login");
    login_button.style.display = "none";
    hide_other_pages();
    home_page.style.display = "none";
    login_page.style.display = "flex";
    
    hide_other_navs();
}

function admin_home() {
    hide_other_pages();
    hide_other_navs();

    var admin_page = document.getElementById("admin_page");
    let nav = document.getElementById("admin-nav");

    nav.style.display = "flex";
    nav.style.justifyContent = "center";
    admin_page.style.display = "block";

    document.getElementById("admin-home-btn").style.display = "none";
}

function button_verify_login() {
    // Login button calls this function
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
        } else { 
            warning2.textContent = "Password invalid.";
        }
    } else { 
        warning1.textContent = "Enter a valid username";
    }
}

function button_manage_testimonials() {
    hide_other_pages();
    document.getElementById("manage_testimonials").style.display = "block";

    let nav = document.getElementById("admin-nav");
    nav.style.display = "flex";
    nav.style.justifyContent = "center";

    document.getElementById("admin-home-btn").style.display = "inline-flex";

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

// ========================================
// OTHER FUNCTIONS
// ========================================

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
/* =========================================================
LOGOUT CONFIRM 
- Admin clicks "Logout" => opens confirm modal
- Confirm => logout + go to HOME user page
- Cancel/Close => stay where you are
   ========================================================= */

function button_logout() {
    open_modal("modal-logout-confirm");
}

function confirm_logout() {
  // clear any admin-only UI state
    clear_unsaved_changes();
    pending_navigation_action = null;

  // close the modal
    close_modal("modal-logout-confirm");

  // go to public home page
    button_home();

  // optional: friendly toast
    show_toast("Logged out successfully!");
}

function cancel_logout() {
    close_modal("modal-logout-confirm");
}

//* =========================================================EXTERNAL LINKS====================================================*//

function open_whatsapp() {
    const whatsapp_link = "https://chat.whatsapp.com/ILZKXRuiixA3yJYpq1Xteb";
    window.open(whatsapp_link, "_blank");
}

function open_instagram() {
    const instagram_link = "https://www.instagram.com/saltypadel/";
    window.open(instagram_link, "_blank");
}

/* Open shop ONLY after user confirms in modal */
function open_shop_with_warning() {
    pending_external_url = "https://vx3.co.uk/collections/salty-padel";
    show_toast("Opening shop link…"); 
    open_modal("modal-external-warning");
}

function open_external_link_with_warning(url) {
    pending_external_url = url;
    show_toast("Opening shop link…"); 
    open_modal("modal-external-warning");
}

function setup_external_warning_modal_buttons() {
    const confirm_button = document.getElementById("external-link-confirm");
    if (!confirm_button) return;

    confirm_button.onclick = function (event) {
    if (event) event.preventDefault();

    if (pending_external_url) {
        const new_tab = window.open(pending_external_url, "_blank");

      // if popups blocked, tell the user
    if (!new_tab) {
        show_toast("Pop-up blocked. Please allow pop-ups for this site.", "error");
    } else {
        show_toast("Opened in a new tab!");
    }

    pending_external_url = null;
    }

    close_modal("modal-external-warning");
};

  // OPTIONAL: if you want cancel button to also clear pending url
if (cancel_button) {
    cancel_button.onclick = function (event) {
    if (event) event.preventDefault();
    pending_external_url = null;
    close_modal("modal-external-warning");
    show_toast("Cancelled.");
    };
}
}

/* =========================================================
MODALS
   ========================================================= */

/* Prevent crash if mobile menu not implemented yet */
function close_mobile_menu() {
  // no mobile menu yet - safe empty function
}

function open_modal(modal_id) {
    const modal = document.getElementById(modal_id);
    if (!modal) return;

    modal.classList.add("active");
    document.body.classList.add("modal-open");

    close_mobile_menu();
}

function close_modal(modal_id) {
    const modal = document.getElementById(modal_id);
    if (!modal) return;

    modal.classList.remove("active");
    document.body.classList.remove("modal-open");
}

function setup_modal_click_outside() {
    const overlays = document.querySelectorAll(".modal-overlay");
    overlays.forEach((overlay) => {
    overlay.addEventListener("click", function (event) {
        if (event.target === overlay) {
        close_modal(overlay.id);
        }   
    });
});
}

document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
    const active_modals = document.querySelectorAll(".modal-overlay.active");
    active_modals.forEach((modal) => close_modal(modal.id));
    }
});

/* =========================================================
TOASTS
   ========================================================= */
function show_toast(message, type = "default") {
    const area = document.getElementById("toast-area");
    if (!area) return;

    const toast = document.createElement("div");
    toast.className = "toast" + (type === "error" ? " toast-error" : "");

    toast.innerHTML = `
    <div>
        <div class="toast-title">${type === "error" ? "Error" : "Notice"}</div>
        <div class="toast-msg">${message}</div>
    </div>
    <button class="toast-close" aria-label="Close">&times;</button>
`;

area.appendChild(toast);

    const close_btn = toast.querySelector(".toast-close");
    if (close_btn) close_btn.onclick = () => toast.remove();

    setTimeout(() => {
    if (toast && toast.parentNode) toast.remove();
}, 3000);
}

/* =========================================================
UPLOAD TESTIMONIAL (placeholder)
- Requires in HTML:
textarea id="testimonial_text"
- Add oninput="mark_unsaved_changes()" on the textarea
   ========================================================= */
function upload_testimonial() {
    const text_el = document.getElementById("testimonial_text");
    const text = text_el ? text_el.value.trim() : "";

    if (text === "") {
    show_toast("Please fill all required fields", "error");
    return;
}

  // Simulate saving
clear_unsaved_changes();
show_toast("Changes saved successfully!");
}

// =======================================================
// SINGLE INIT (IMPORTANT: only one DOMContentLoaded)
// =======================================================
document.addEventListener("DOMContentLoaded", function() {

    // Setup modal systems
    setup_modal_click_outside();
    setup_unsaved_modal_buttons();
    setup_external_warning_modal_buttons();

    // Default page OR hash page
    hide_other_pages();
    hide_other_navs();

    if (location.hash === '#who') {
        button_who_we_are();
    } else {
        document.getElementById("home_page").style.display = "block";
        document.getElementById("main-nav").style.display = "flex";
    }
});