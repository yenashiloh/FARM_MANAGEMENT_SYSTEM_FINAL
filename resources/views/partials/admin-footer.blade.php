{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script src="{{ ../../../../asset('../../../../assets/js/main.js') }}"></script>
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleSubmenus = document.querySelectorAll('.toggle-submenu');

    toggleSubmenus.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const subMenu = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (subMenu.style.display === 'block') {
                subMenu.style.display = 'none';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            } else {
                subMenu.style.display = 'block';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        });
    });
});

//Reports sub menu
document.addEventListener('DOMContentLoaded', function() {
    const reportsLink = document.getElementById('reportsLink');
    const reportsSubmenu = document.getElementById('reportsSubmenu');
    
    reportsLink.addEventListener('click', function(e) {
        e.preventDefault();
        reportsSubmenu.classList.toggle('show-submenu');
        maintenanceSubmenu.classList.remove('show-submenu');
    });

    const maintenanceLink = document.getElementById('maintenanceLink');
    const maintenanceSubmenu = document.getElementById('maintenanceSubmenu');

    maintenanceLink.addEventListener('click', function(e) {
        e.preventDefault();
        maintenanceSubmenu.classList.toggle('show-submenu');
        reportsSubmenu.classList.remove('show-submenu');
    });

    document.addEventListener('click', function(e) {
        if (!reportsLink.contains(e.target) && !reportsSubmenu.contains(e.target) && !maintenanceLink.contains(e.target) && !maintenanceSubmenu.contains(e.target)) {
            reportsSubmenu.classList.remove('show-submenu');
            maintenanceSubmenu.classList.remove('show-submenu');
        }
    });

    var alert = document.getElementById('success-alert');
    if (alert) {
        setTimeout(function() {
            $(alert).alert('close');
        }, 3000); 
    }
});




</script> --}}

<!-- jquery 3.3.1 -->
<script src="../../../../asset/vendor/jquery/jquery-3.3.1.min.js"></script>
<!-- bootstap bundle js -->
<script src="../../../../asset/vendor/bootstrap/js/bootstrap.bundle.js"></script>
<!-- slimscroll js -->
<script src="../../../../asset/vendor/slimscroll/jquery.slimscroll.js"></script>
<!-- main js -->
<script src="../../../../asset/libs/js/main-js.js"></script>
<!-- chart chartist js -->
<script src="../../../../asset/vendor/charts/chartist-bundle/chartist.min.js"></script>
<!-- sparkline js -->
<script src="../../../../asset/vendor/charts/sparkline/jquery.sparkline.js"></script>
<!-- morris js -->
<script src="../../../../asset/vendor/charts/morris-bundle/raphael.min.js"></script>
<script src="../../../../asset/vendor/charts/morris-bundle/morris.js"></script>
<!-- chart c3 js -->
<script src="../../../../asset/vendor/charts/c3charts/c3.min.js"></script>
<script src="../../../../asset/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
<script src="../../../../asset/vendor/charts/c3charts/C3chartjs.js"></script>
<script src="../../../../asset/libs/js/dashboard-ecommerce.js"></script>
<script src="{{ asset('../../../../assets/js/main.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
