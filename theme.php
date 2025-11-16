<!-- theme.php : Global Theme Manager -->

<!-- ================= DARK MODE CSS ================= -->
<style>
/* Floating Round Dark Mode Toggle */
.floating-theme-btn {
    position: fixed;
    bottom: 25px;
    right: 25px;
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: #222;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    transition: background 0.3s ease, transform 0.2s ease;
    z-index: 9999;
}

.floating-theme-btn:hover {
    transform: scale(1.07);
}

.floating-theme-btn .icon {
    position: absolute;
    font-size: 26px;
    transition: 0.3s ease;
}

/* Light mode default icons */
.floating-theme-btn .sun {
    opacity: 1;
    transform: rotate(0deg);
}

.floating-theme-btn .moon {
    opacity: 0;
    transform: rotate(-180deg);
}

/* Dark mode button */
body.dark .floating-theme-btn {
    background: #eee;
    color: #000;
}

/* Icon change animation */
body.dark .floating-theme-btn .sun {
    opacity: 0;
    transform: rotate(180deg);
}

body.dark .floating-theme-btn .moon {
    opacity: 1;
    transform: rotate(0deg);
}

/* ===== Main Dark Mode Styles ===== */
body.dark {
    background-color: #121212 !important;
    color: #e6e6e6 !important;
}

/* Cards, lists, dropdowns */
body.dark .card,
body.dark .list-group-item,
body.dark .dropdown-menu,
body.dark .modal-content {
    background-color: #1c1c1c !important;
    color: #e6e6e6 !important;
    border-color: #333 !important;
}

/* Form controls */
body.dark input,
body.dark textarea,
body.dark select,
body.dark .form-control {
    background-color: #1e1e1e !important;
    color: #fff !important;
    border: 1px solid #444 !important;
}

body.dark .form-control::placeholder {
    color: #aaa !important;
}

/* Buttons */
body.dark .btn {
    border-color: #fff !important;
    color: #fff !important;
}

/* Primary button stays primary */
body.dark .btn-primary {
    background-color: #0d6efd !important;
}

/* Utility backgrounds */
body.dark .bg-light {
    background-color: #1c1c1c !important;
}

/* Tables */
body.dark table {
    color: #eee !important;
}

body.dark table tr {
    background-color: #1e1e1e !important;
}

body.dark table td, 
body.dark table th {
    border-color: #333 !important;
}
</style>


<!-- ================= DARK MODE TOGGLE BUTTON ================= -->
<div class="floating-theme-btn" id="themeToggle">
    <div class="icon sun">☀️</div>
    <div class="icon moon">🌙</div>
</div>


<!-- ================= DARK MODE SCRIPT ================= -->
<script>
// Apply saved theme
if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
}

// Toggle theme on click
document.getElementById("themeToggle").addEventListener("click", () => {
    document.body.classList.toggle("dark");

    if (document.body.classList.contains("dark")) {
        localStorage.setItem("theme", "dark");
    } else {
        localStorage.setItem("theme", "light");
    }
});
</script>
