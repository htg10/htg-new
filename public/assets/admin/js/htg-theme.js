/* ==========================================================================
   HTG LEDGER — behaviour layer
   Loads AFTER the Skote app.js. Owns: sidebar toggle, light/dark mode,
   flash messages. Nothing here touches form data, routes or server state.
   ========================================================================== */
(function () {
    'use strict';

    var STORE = 'htg.theme';
    var root = document.documentElement;

    /* ----------------------------------------------------------------------
       Light / dark
       The <head> inline snippet already applied the stored value before paint,
       so there is no flash. This just wires the switch.
       ---------------------------------------------------------------------- */
    function currentTheme() {
        return root.getAttribute('data-htg-theme') === 'dark' ? 'dark' : 'light';
    }

    function applyTheme(mode) {
        root.setAttribute('data-htg-theme', mode);
        document.body.setAttribute('data-layout-mode', mode);

        try { localStorage.setItem(STORE, mode); } catch (e) {}

        var icon = document.querySelector('#htg-theme-btn i');
        if (icon) {
            icon.className = mode === 'dark' ? 'bx bx-sun' : 'bx bx-moon';
        }

        var btn = document.getElementById('htg-theme-btn');
        if (btn) {
            btn.setAttribute('title', mode === 'dark' ? 'Switch to light' : 'Switch to dark');
            btn.setAttribute('aria-label', btn.getAttribute('title'));
        }

        // Google Charts have baked-in colours; pages that draw charts listen
        // for this and redraw themselves.
        window.dispatchEvent(new CustomEvent('htg:themechange', { detail: { mode: mode } }));
    }

    window.htgTheme = { get: currentTheme, set: applyTheme };

    /* ----------------------------------------------------------------------
       Sidebar
       Skote's app.js binds #vertical-menu-btn. Our button is #htg-menu-btn so
       the two never both fire on the same click (that cancelled itself out).
       ---------------------------------------------------------------------- */
    function toggleSidebar() {
        var b = document.body;

        if (window.innerWidth <= 991) {
            b.classList.toggle('sidebar-enable');
            b.classList.remove('vertical-collpsed');
        } else {
            b.classList.toggle('sidebar-enable');
            b.classList.toggle('vertical-collpsed');
            try {
                localStorage.setItem('htg.sidebar', b.classList.contains('vertical-collpsed') ? 'rail' : 'full');
            } catch (e) {}
        }
    }

    function closeSidebarOnOutsideClick(e) {
        if (window.innerWidth > 991) return;
        if (!document.body.classList.contains('sidebar-enable')) return;

        var menu = document.querySelector('.vertical-menu');
        var btn = document.getElementById('htg-menu-btn');
        if (!menu) return;
        if (menu.contains(e.target) || (btn && btn.contains(e.target))) return;

        document.body.classList.remove('sidebar-enable');
    }

    /* ----------------------------------------------------------------------
       Flash messages
       ---------------------------------------------------------------------- */
    function flash() {
        var el = document.getElementById('htg-flash');
        if (!el || typeof Swal === 'undefined') return;

        var text = (el.getAttribute('data-message') || '').trim();
        if (!text) return;

        Swal.fire({
            text: text,
            icon: el.getAttribute('data-type') || 'success',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3600,
            timerProgressBar: true
        });
    }

    /* ----------------------------------------------------------------------
       Boot
       ---------------------------------------------------------------------- */
    document.addEventListener('DOMContentLoaded', function () {
        applyTheme(currentTheme());

        var themeBtn = document.getElementById('htg-theme-btn');
        if (themeBtn) {
            themeBtn.addEventListener('click', function () {
                applyTheme(currentTheme() === 'dark' ? 'light' : 'dark');
            });
        }

        var menuBtn = document.getElementById('htg-menu-btn');
        if (menuBtn) menuBtn.addEventListener('click', toggleSidebar);

        // Restore the rail state on desktop.
        try {
            if (window.innerWidth > 991 && localStorage.getItem('htg.sidebar') === 'rail') {
                document.body.classList.add('vertical-collpsed', 'sidebar-enable');
            }
        } catch (e) {}

        document.addEventListener('click', closeSidebarOnOutsideClick);

        flash();
    });
})();
