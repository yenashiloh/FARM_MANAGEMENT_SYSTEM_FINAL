{{-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script src="{{ asset('assets/js/main.js') }}"></script>
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
    });

    document.addEventListener('click', function(e) {
        if (!reportsLink.contains(e.target) && !reportsSubmenu.contains(e.target)) {
            reportsSubmenu.classList.remove('show-submenu');
        }
    });
});



</script> --}}

