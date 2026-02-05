// ========================================
// GLOBAL STATE
// ========================================
let has_unsaved_changes = false;
let pending_navigation_action = null;
let pending_external_url = null;
let pendingDeleteAction = null;

// ========================================
// DOM READY - INITIALIZE EVERYTHING
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    
    // Modal close on outside click
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('active');
            document.body.classList.remove('modal-open');
        }
    });

    console.log('Modal and Toast system initialized');
});

// ========================================
// HELPER FUNCTIONS
// ========================================
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
    safe_hide("home_page");
    safe_hide("who_we_are");
    safe_hide("what_we_do");
    safe_hide("past_events");
    safe_hide("upcoming_events");
    safe_hide("login_page");
    safe_hide("admin_page");
    safe_hide("manage_testimonials");
    safe_hide("manage_partners");
    safe_hide("manage_past_events");
    safe_hide("manage_upcoming_events");
}

function hide_other_navs() {
    safe_hide("main-nav");
    safe_hide("who-are-we-nav");
    safe_hide("what-we-do-nav");
    safe_hide("past-events-nav");
    safe_hide("upcoming-events-nav");
    safe_hide("admin-nav");
}

// ========================================
// MOBILE MENU FUNCTIONS
// ========================================
function toggle_mobile_menu() {
    const dropdown = document.getElementById('mobile-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('active');
    }
}

function close_mobile_menu() {
    const dropdown = document.getElementById('mobile-dropdown');
    if (dropdown) {
        dropdown.classList.remove('active');
    }
}

function update_mobile_nav(current_page) {
    // Update mobile menu button visibility based on current page
    const buttons = {
        'home': document.getElementById('mobile-nav-home'),
        'who_we_are': document.getElementById('mobile-nav-who-we-are'),
        'what_we_do': document.getElementById('mobile-nav-what-we-do'),
        'past_events': document.getElementById('mobile-nav-past-events'),
        'upcoming_events': document.getElementById('mobile-nav-upcoming-events')
    };
    
    // Show all buttons
    Object.values(buttons).forEach(btn => {
        if (btn) btn.style.display = 'block';
    });
    
    // Hide current page button
    if (buttons[current_page]) {
        buttons[current_page].style.display = 'none';
    }
}

// ========================================
// PAGE NAVIGATION FUNCTIONS
// ========================================

function button_home() {
    hide_other_pages();
    safe_show("home_page", "block");

    hide_other_navs();
    let nav = document.getElementById("main-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    // Show admin login button in footer
    const adminLoginBtn = document.getElementById("admin-login");
    if (adminLoginBtn) {
        adminLoginBtn.style.display = "block";
    }

    // Update mobile navigation
    update_mobile_nav('home');
}

function button_who_we_are() {
    hide_other_pages();
    safe_show("who_we_are", "block");

    hide_other_navs();
    let nav = document.getElementById("who-are-we-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    update_mobile_nav('who_we_are');
}

function button_what_we_do() {
    hide_other_pages();
    safe_show("what_we_do", "block");

    hide_other_navs();
    let nav = document.getElementById("what-we-do-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    update_mobile_nav('what_we_do');
}

function button_past_events() {
    hide_other_pages();
    safe_show("past_events", "block");

    hide_other_navs();
    let nav = document.getElementById("past-events-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    update_mobile_nav('past_events');
}

function button_upcoming_events() {
    hide_other_pages();
    safe_show("upcoming_events", "block");

    hide_other_navs();
    let nav = document.getElementById("upcoming-events-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    update_mobile_nav('upcoming_events');
}

// ========================================
// ADMIN FUNCTIONS
// ========================================

function button_admin_login() {
    hide_other_pages();
    safe_show("login_page", "flex");
    
    const loginButton = document.getElementById("admin-login");
    if (loginButton) {
        loginButton.style.display = "none";
    }

    hide_other_navs();
}

function admin_home() {
    hide_other_pages();
    hide_other_navs();
    
    safe_show("admin_page", "block");
    
    let nav = document.getElementById("admin-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    const adminHomeBtn = document.getElementById("admin-home-btn");
    if (adminHomeBtn) {
        adminHomeBtn.style.display = "none";
    }
}

function button_verify_login() {
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
            showToast('Welcome back!', 'success');
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
    safe_show("manage_testimonials", "block");

    let nav = document.getElementById("admin-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    const adminHomeBtn = document.getElementById("admin-home-btn");
    if (adminHomeBtn) {
        adminHomeBtn.style.display = "inline-flex";
    }
}

function button_manage_past_events() {
    hide_other_pages();
    safe_show("manage_past_events", "block");

    let nav = document.getElementById("admin-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    const adminHomeBtn = document.getElementById("admin-home-btn");
    if (adminHomeBtn) {
        adminHomeBtn.style.display = "block";
    }
}

function button_manage_upcoming_events() {
    hide_other_pages();
    safe_show("manage_upcoming_events", "block");

    let nav = document.getElementById("admin-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    const adminHomeBtn = document.getElementById("admin-home-btn");
    if (adminHomeBtn) {
        adminHomeBtn.style.display = "block";
    }
}

function button_manage_partners() {
    hide_other_pages();
    safe_show("manage_partners", "block");

    let nav = document.getElementById("admin-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    const adminHomeBtn = document.getElementById("admin-home-btn");
    if (adminHomeBtn) {
        adminHomeBtn.style.display = "inline-flex";
    }
}

// ========================================
// MODAL SYSTEM
// ========================================

function open_modal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.classList.add('modal-open');
    }
}

function close_modal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.classList.remove('modal-open');
    }
}

// ========================================
// EXTERNAL LINK MODAL
// ========================================

function openExternalLink(url) {
    pending_external_url = url;
    open_modal('modal-external-warning');
    
    document.getElementById('external-link-confirm').onclick = function() {
        window.open(pending_external_url, '_blank');
        close_modal('modal-external-warning');
        pending_external_url = null;
    };
}

function open_instagram() {
    openExternalLink('https://www.instagram.com/saltypadel/');
}

function open_shop_with_warning() {
    openExternalLink('https://vx3.co.uk/collections/salty-padel');
}

// ========================================
// ADMIN LOGOUT MODAL
// ========================================

function trigger_logout() {
    open_modal('modal-logout-confirm');
}

function cancel_logout() {
    close_modal('modal-logout-confirm');
}

function confirm_logout() {
    close_modal('modal-logout-confirm');
    showToast('Logged out successfully', 'success');
    
    setTimeout(function() {
        button_home();
    }, 500);
}

// ========================================
// DELETE CONFIRMATION MODAL
// ========================================

function confirmDelete(itemName, deleteCallback) {
    const modalBody = document.querySelector('#modal-confirm-delete .modal-body');
    if (modalBody) {
        modalBody.textContent = `Delete "${itemName}"? This can't be undone.`;
    }
    
    pendingDeleteAction = deleteCallback;
    open_modal('modal-confirm-delete');
    
    const deleteBtn = document.getElementById('delete-confirm-btn');
    if (deleteBtn) {
        deleteBtn.onclick = function() {
            if (pendingDeleteAction) {
                pendingDeleteAction();
                showToast('Item deleted successfully', 'success');
                close_modal('modal-confirm-delete');
                pendingDeleteAction = null;
            }
        };
    }
}

// ========================================
// TOAST SYSTEM
// ========================================

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    toast.innerHTML = `
        <div>
            <div class="toast-title">${type === 'success' ? 'Success' : 'Error'}</div>
            <div class="toast-msg">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    const toastArea = document.getElementById('toast-area');
    if (toastArea) {
        toastArea.appendChild(toast);
    }
    
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(400px)';
        setTimeout(function() {
            toast.remove();
        }, 300);
    }, 3000);
}

function showSuccessToast(message) {
    showToast(message, 'success');
}

function showErrorToast(message) {
    showToast(message, 'error');
}

function toastLoginSuccess() {
    showToast('Welcome back!', 'success');
}

function toastSaveSuccess() {
    showToast('Changes saved', 'success');
}

function toastUploadSuccess() {
    showToast('Upload complete', 'success');
}

function toastError() {
    showToast('Something went wrong. Please try again.', 'error');
}