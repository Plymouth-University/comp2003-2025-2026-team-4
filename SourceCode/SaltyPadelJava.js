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
    safe_hide("manage_events");
    safe_hide("manage_whatsapp"); 
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
    document.body.classList.remove('admin-mode'); // safety
    hide_other_pages();
    safe_show("past_events", "block");

    hide_other_navs();
    let nav = document.getElementById("past-events-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    update_mobile_nav('past_events');
    close_mobile_menu();
}

function button_upcoming_events() {
    document.body.classList.remove('admin-mode'); // safety
    hide_other_pages();
    safe_show("upcoming_events", "block");

    hide_other_navs();
    let nav = document.getElementById("upcoming-events-nav");
    if (nav) {
        nav.style.display = "flex";
        nav.style.justifyContent = "center";
    }

    update_mobile_nav('upcoming_events');
    close_mobile_menu();
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

    document.body.classList.add('admin-mode');
    
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

async function button_verify_login() {
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
            document.body.classList.add('admin-mode');
            showToast('Welcome back!', 'success');
            admin_home();
        } else {
            warning2.textContent = "Password invalid.";
        }
    } else {
        warning1.textContent = "Enter a valid username";
    }

    try {
        const API_URL = 'http://localhost/api/v1/login';
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: document.getElementById("admin_username").value,
                password: document.getElementById("admin_password").value
            })
        });

        const result = await response.json();
        
        if (response.ok && result.success && result.data && result.data.token) {
            const token = result.data.token;
            sessionStorage.setItem('auth-token', token);
            document.body.classList.add('admin-mode');
            showToast('Welcome back!', 'success');
            admin_home();
        }
    }
    catch (error) {
        console.error('Error:', error);
        const status = error.status

        if (status == 400 || status == 404) {
            showToast("Invalid credentials. Please try again.");
            warning2.textContent = "Invalid credentials";
        }
        else if (status == 429) {
            showToast("Too many repeated attempts. Please try again later.");
        }
        else {showToast("Server error. Please try again later.");}
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


//====================
// Testimonial upload
//====================
function uploads_hide_helper(targetDiv) {
    const divsToHide = document.querySelectorAll(".input_detail");
    if (divsToHide) {
        divsToHide.forEach(div => {
            div.style.display = "none";
        });
    }
    if (targetDiv) {
        targetDiv.style.display = "flex";
    }
}
function button_testimonial_name() {
    targetDiv = document.getElementById("testimonial-name-input");
    uploads_hide_helper(targetDiv)
}
function button_confirm_testimonial_name() {
    const name = document.getElementById('testimonial-name-textInput').value;
    const namePreview = document.getElementById('testimonial-name-preview');
    namePreview.textContent = name;
    sessionStorage.setItem('testimonial-name', name);
    uploads_hide_helper();
}

function button_testimonial_competition() {
    targetDiv = document.getElementById("testimonial-competition-input");
    uploads_hide_helper(targetDiv)
}

function button_confirm_testimonial_competition() {
    const competition = document.getElementById('testimonial-competition-textInput').value;
    const competitionPreview = document.getElementById('testimonial-competition-preview');
    competitionPreview.textContent = competition;
    sessionStorage.setItem('testimonial-competition', competition);
    uploads_hide_helper();
}
function button_testimonial_upload() {
    const testimonialCompetition = sessionStorage.getItem('testimonial-competition');
    const testimonialName = sessionStorage.getItem('testimonial-name');
    const testimonialPhoto = sessionStorage.getItem('testimonial-photo');
    const testimonialTextInput = document.getElementById('testimonial-text-input').value;
    if (!testimonialCompetition || !testimonialName || !testimonialPhoto || !testimonialTextInput) {
        try {
            // POST
            const status = error.status;
            if (status == 201) {
                showToast("Testimonial uploaded successfully!", "success");
            }
        } catch (error) {
            console.error('Error:', error);
            const status = error.status;
            if (status == 403) {
                showToast("Forbidden. You do not have permission to perform this action.");
            }
            else if (status == 401) {
                showToast("Unauthorized. Your token may have expired. Try logging in again.");
            }
            else { showToast("Server error. Please try again later."); }
        }
    }
}

function button_confirm_testimonial_variable() {
    const name = document.getElementById('partner-name-textInput').value;
    const namePreview = document.getElementById('partner-name-preview');
    namePreview.textContent = name;
    sessionStorage.setItem('partner-name', name);
    testimonials_hide_helper();
}

function button_testimonial_photo() {
    let fileInput = document.getElementById('testimonial-photo-input');
    if (!fileInput) {
        fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.id = 'testimonial-photo-input';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';
        fileInput.onchange = handle_testimonial_photo;
        document.body.appendChild(fileInput);
    }
    fileInput.click();
}

function handle_testimonial_photo(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const previewImg = document.getElementById('testimonial-image-preview');
            if (previewImg) {
                previewImg.style.backgroundImage = `url('${e.target.result}')`;
                previewImg.style.backgroundSize = 'cover';
                previewImg.style.backgroundPosition = 'center';
                showToast('Photo uploaded successfully', 'success');
            }
            sessionStorage.setItem('testimonial-photo', e.target.result);
        };
        reader.readAsDataURL(file);
    } else {
        showToast('Please select a valid image file', 'error');
    }
}
//====================
// Event upload
//====================

function button_event_title_location() {
    targetDiv = document.getElementById("event-title-location-input");
    uploads_hide_helper(targetDiv);
}

function button_confirm_event_title_location() {
    const title = document.getElementById('event-title-textInput').value;
    const location = document.getElementById('event-location-textInput').value;
    const titlePreview = document.getElementById('event-title-preview');
    const locationPreview = document.getElementById('event-location-preview');
    titlePreview.textContent = title;
    locationPreview.textContent = location;
    sessionStorage.setItem('event-title', title);
    sessionStorage.setItem('event-location', location);
    uploads_hide_helper();
}

function button_event_date() {
    targetDiv = document.getElementById("event-date-input");
    uploads_hide_helper(targetDiv);
}

function button_confirm_event_date() {
    const date = document.getElementById('event-date-dateInput').value;
    const datePreview = document.getElementById('event-date-preview');
    datePreview.textContent = date;
    sessionStorage.setItem('event-date', date);
    uploads_hide_helper();
}

function button_event_photo() {
    let fileInput = document.getElementById('event-photo-input');
    if (!fileInput) {
        fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.id = 'event-photo-input';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';
        fileInput.onchange = handle_event_photo;
        document.body.appendChild(fileInput);
    }
    fileInput.click();
}
function handle_event_photo(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const previewImg = document.querySelector('#manage_events .gallery-item img');
            if (previewImg) {
                previewImg.src = e.target.result;
                showToast('Event poster uploaded successfully', 'success');
            }
            sessionStorage.setItem('event-poster', e.target.result);
        };
        reader.readAsDataURL(file);
    } else {
        showToast('Please select a valid image file', 'error');
    }
}

function button_upload_event() {
    const eventTitle = sessionStorage.getItem('event-title');
    const eventLocation = sessionStorage.getItem('event-location');
    const eventDate = sessionStorage.getItem('event-date');
    const eventPoster = sessionStorage.getItem('event-poster');
    if (!eventTitle || !eventLocation || !eventDate || !eventPoster) {
        try {
            // POST
            const status = error.status;
            if (status == 201) {
                showToast("Event uploaded successfully!", "success");
                //go to home
            }
        } catch (error) {
            console.error('Error:', error);
            const status = error.status;
            if (status == 403) {
                showToast("Forbidden. You do not have permission to perform this action.");
            }
            else if (status == 401) {
                showToast("Unauthorized. Your token may have expired. Try logging in again.");
            }
            else { showToast("Server error. Please try again later."); }
        }
    }
}

//====================
// Partner upload
//====================

function button_partner_name() {
    targetDiv = document.getElementById("partner-name-input");
    uploads_hide_helper(targetDiv)
}

function button_confirm_partner_name() {
    const name = document.getElementById('partner-name-textInput').value;
    const namePreview = document.getElementById('partner-name-preview');
    namePreview.textContent = name;
    sessionStorage.setItem('partner-name', name);
    uploads_hide_helper();
}
function button_partner_photo() {
    let fileInput = document.getElementById('partner-photo-input');
    if (!fileInput) {
        fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.id = 'partner-photo-input';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';
        fileInput.onchange = handle_partner_photo;
        document.body.appendChild(fileInput);
    }
    fileInput.click();
}

function button_upload_partner() {
    const partnerName = sessionStorage.getItem('partner-name');
    const partnerPhoto = sessionStorage.getItem('partner-photo');
    if (!partnerName || !partnerPhoto) {
        try {
            // POST
            const status = error.status;
            if (status == 201) {
                showToast("Partner uploaded successfully!", "success");
            }
        } catch (error) {
            console.error('Error:', error);
            const status = error.status;
            if (status == 403) {
                showToast("Forbidden. You do not have permission to perform this action.");
            }
            else if (status == 401) {
                showToast("Unauthorized. Your token may have expired. Try logging in again.");
            }
            else { showToast("Server error. Please try again later."); }
        }
    }
}
function handle_partner_photo(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const previewImg = document.querySelector('#manage_partners .gallery-item img');
            if (previewImg) {
                previewImg.src = e.target.result;
                showToast('Partner logo uploaded successfully', 'success');
            }
            sessionStorage.setItem('partner-logo', e.target.result);
        };
        reader.readAsDataURL(file);
    } else {
        showToast('Please select a valid image file', 'error');
    }
}

function button_manage_events() {
    hide_other_pages();
    safe_show("manage_events", "block");

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
function button_manage_whatsapp() {
    hide_other_pages();
    safe_show("manage_whatsapp", "block");

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

function save_whatsapp_link() {
    const input = document.getElementById('whatsapp-link-input');
    const newLink = input.value.trim();
    
    // Basic validation
    if (!newLink) {
        showToast('Please enter a WhatsApp link', 'error');
        return;
    }
    
    if (!newLink.includes('whatsapp.com')) {
        showToast('Please enter a valid WhatsApp link', 'error');
        return;
    }
    
    // Here you would normally save to database
    // For now, just show success message
    showToast('WhatsApp link updated successfully!', 'success');
    
    console.log('New WhatsApp link:', newLink);
    // TODO: Add backend integration to actually save the link
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
    document.body.classList.remove('admin-mode');
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