</div>
</div>
</div>

<script>
    (function () {
        var sidebar = document.getElementById('mainSidebar');
        var backdrop = document.getElementById('sidebarBackdrop');
        var hamburgerBtn = document.getElementById('hamburgerBtn');
        var body = document.body;
        if (!sidebar) return;

        var isSidebarOpen = false;

        function openSidebar() {
            if (window.innerWidth >= 992) return;
            sidebar.classList.add('open');
            if (backdrop) backdrop.classList.add('show');
            isSidebarOpen = true;
            body.classList.add('menu-open');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            if (backdrop) backdrop.classList.remove('show');
            isSidebarOpen = false;
            body.classList.remove('menu-open');
        }

        function toggleSidebar() { isSidebarOpen ? closeSidebar() : openSidebar(); }

        if (backdrop) backdrop.addEventListener('click', closeSidebar);
        if (hamburgerBtn) hamburgerBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleSidebar();
        });
    })();
</script>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>