<?php
// views/footer.php

$app_name = function_exists('getAppName') ? getAppName() : 'Sistem Inventaris Barang';
$app_version = function_exists('getAppVersion') ? getAppVersion() : '2.0.0';
?>
        </div> <!-- /#content -->
        
        <!-- FOOTER -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span style="font-size: 12px;">
                        <i class="fas fa-boxes" style="color: #2c6b9e;"></i>
                        <span style="color: #1a2634; font-weight: 500;"><?= $app_name ?></span>
                        <span style="color: #8a94a6;">&copy; <?= date('Y'); ?></span>
                        <span style="color: #d1d5db; margin: 0 8px;">|</span>
                        <span style="color: #8a94a6; font-size: 11px;">
                            <i class="fas fa-code"></i> v<?= $app_version ?>
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
<script src="assets/js/ruang-admin.min.js"></script>

<!-- ============================================ -->
<!-- SCRIPT TOGGLE SIDEBAR - RESPONSIVE -->
<!-- ============================================ -->
<script>
$(document).ready(function() {
    
    // ============================================
    // TOGGLE SIDEBAR MOBILE - PASTIKAN INI
    // ============================================
    
    // Fungsi toggle sidebar
    function toggleSidebar() {
        $('.sidebar').toggleClass('show');
        $('#sidebarOverlay').toggleClass('show');
    }
    
    // Tombol hamburger
    $('#sidebarToggleTop').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
        console.log('Sidebar toggled'); // Debug
    });
    
    // Overlay - tutup sidebar saat diklik
    $('#sidebarOverlay').on('click', function() {
        $('.sidebar').removeClass('show');
        $('#sidebarOverlay').removeClass('show');
    });
    
    // Tutup sidebar saat resize ke desktop
    $(window).on('resize', function() {
        if ($(window).width() >= 769) {
            $('.sidebar').addClass('show');
            $('#sidebarOverlay').removeClass('show');
        } else {
            $('.sidebar').removeClass('show');
            $('#sidebarOverlay').removeClass('show');
        }
    });
    
    // Inisialisasi sidebar di desktop
    if ($(window).width() >= 769) {
        $('.sidebar').addClass('show');
    }
    
    // ============================================
    // DROPDOWN USER
    // ============================================
    $('#userDropdown').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).parent().find('.dropdown-menu').toggleClass('show');
    });
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
    
    // ============================================
    // AUTO CLOSE SIDEBAR SAAT KLIK DI LUAR
    // ============================================
    $(document).on('click', function(e) {
        if ($(window).width() < 769) {
            if (!$(e.target).closest('.sidebar').length && 
                !$(e.target).closest('#sidebarToggleTop').length) {
                $('.sidebar').removeClass('show');
                $('#sidebarOverlay').removeClass('show');
            }
        }
    });
    
});
</script>

</body>
</html>