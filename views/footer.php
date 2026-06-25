        </div> <!-- /#content -->
        
        <!-- ============================================ -->
        <!-- FOOTER -->
        <!-- ============================================ -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>
                        <i class="fas fa-heartbeat" style="color: #dc3545;"></i>
                        <span style="color: #1a2634; font-weight: 500;">E-Posyandu Bougenvil Belik</span>
                        <span style="color: #8a94a6;">&copy; <?= date('Y'); ?></span>
                        <span style="color: #d1d5db; margin: 0 8px;">|</span>
                        <span style="color: #8a94a6; font-size: 12px;">
                            <i class="fas fa-code"></i> v1.0
                        </span>
                    </span>
                </div>
            </div>
        </footer>

    </div> <!-- /#content-wrapper -->
</div> <!-- /#wrapper -->

<!-- ============================================ -->
<!-- SCRIPTS -->
<!-- ============================================ -->

<!-- jQuery -->
<script src="vendor/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- jQuery Easing -->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<!-- Ruang Admin JS -->
<script src="js/ruang-admin.min.js"></script>

<!-- ============================================ -->
<!-- SCRIPT UNTUK TOGGLE MOBILE -->
<!-- ============================================ -->
<script>
$(document).ready(function() {
    
    // Toggle sidebar untuk mobile
    $('#sidebarToggleTop').on('click', function(e) {
        e.preventDefault();
        $('.sidebar').toggleClass('toggled');
    });
    
    // Auto close sidebar di mobile saat klik di luar
    $(document).on('click', function(e) {
        if ($(window).width() < 768) {
            if (!$(e.target).closest('.sidebar').length && !$(e.target).closest('#sidebarToggleTop').length) {
                $('.sidebar').addClass('toggled');
            }
        }
    });
    
    // Di desktop, sidebar selalu terbuka
    if ($(window).width() >= 768) {
        $('.sidebar').removeClass('toggled');
    }
    
    // Saat resize window
    $(window).on('resize', function() {
        if ($(window).width() >= 768) {
            $('.sidebar').removeClass('toggled');
        }
    });
    
});
</script>

</body>
</html>