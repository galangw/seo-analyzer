$(document).ready(function() {
    // Elements
    const $html = $('html');
    const $lightThemeIcon = $('#lightThemeIcon');
    const $darkThemeIcon = $('#darkThemeIcon');
    
    // Cookie functions
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
    
    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
    // Theme toggling functionality for all users
    function toggleTheme() {
        if ($html.attr('data-bs-theme') === 'dark') {
            $html.attr('data-bs-theme', 'light');
            $lightThemeIcon.addClass('d-none');
            $darkThemeIcon.removeClass('d-none');
            setCookie('theme', 'light', 30);
        } else {
            $html.attr('data-bs-theme', 'dark');
            $darkThemeIcon.addClass('d-none');
            $lightThemeIcon.removeClass('d-none');
            setCookie('theme', 'dark', 30);
        }
    }
    
    // Set theme from cookie or use dark as default for all users
    const savedTheme = getCookie('theme') || 'dark';
    $html.attr('data-bs-theme', savedTheme);
    
    if (savedTheme === 'light') {
        $lightThemeIcon.addClass('d-none');
        $darkThemeIcon.removeClass('d-none');
    } else {
        $darkThemeIcon.addClass('d-none');
        $lightThemeIcon.removeClass('d-none');
    }
    
    // Theme toggle event listeners
    if ($lightThemeIcon.length) $lightThemeIcon.on('click', toggleTheme);
    if ($darkThemeIcon.length) $darkThemeIcon.on('click', toggleTheme);
    
    // Check if user is logged in using the variable passed from Blade
    if (window.appConfig.userLoggedIn) {
        const $sidebar = $('#sidebar');
        const $content = $('#content');
        const $navbarToggleBtn = $('#navbarToggleBtn');
        const $fixedNavbarToggleBtn = $('#fixedNavbarToggleBtn');
        
        // Toggle sidebar functionality
        function toggleSidebar() {
            $sidebar.toggleClass('collapsed');
            $content.toggleClass('expanded');
            
            // Save state to cookie
            const isCollapsed = $sidebar.hasClass('collapsed');
            setCookie('sidebar_collapsed', isCollapsed ? 'true' : 'false', 30);
            
            // Update button icon
            if (isCollapsed) {
                $navbarToggleBtn.html('<i class="bi bi-layout-sidebar-inset"></i>');
                $fixedNavbarToggleBtn.html('<i class="bi bi-layout-sidebar-inset"></i>');
            } else {
                $navbarToggleBtn.html('<i class="bi bi-layout-sidebar"></i>');
                $fixedNavbarToggleBtn.html('<i class="bi bi-layout-sidebar-inset"></i>');
            }
        }
        
        // Initialize sidebar from cookies
        const sidebarCollapsed = getCookie('sidebar_collapsed');
        if (sidebarCollapsed === 'true') {
            $sidebar.addClass('collapsed');
            $content.addClass('expanded');
            $navbarToggleBtn.html('<i class="bi bi-layout-sidebar-inset"></i>');
            $fixedNavbarToggleBtn.html('<i class="bi bi-layout-sidebar-inset"></i>');
        }
        
        // Add sidebar event listeners
        $navbarToggleBtn.on('click', toggleSidebar);
        $fixedNavbarToggleBtn.on('click', toggleSidebar);
        
        // Close sidebar when clicking outside on mobile
        $(document).on('click', function(event) {
            const isClickInsideSidebar = $sidebar.has(event.target).length > 0 || $sidebar.is(event.target);
            const isClickOnToggler = $navbarToggleBtn.has(event.target).length > 0 || $navbarToggleBtn.is(event.target);
            
            if (!isClickInsideSidebar && !isClickOnToggler && $(window).width() < 992 && $sidebar.hasClass('show')) {
                $sidebar.removeClass('show');
            }
        });
        
        // Handle window resize
        $(window).on('resize', function() {
            if ($(window).width() >= 992) {
                $sidebar.removeClass('show');
            }
        });
    }
    
    // Show notification toast if session has success message
    if (window.appConfig.hasSuccessMessage) {
        new bootstrap.Toast($('#notificationToast')[0]).show();
    }
});