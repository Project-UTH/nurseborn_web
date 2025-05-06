<?php
$pageTitle = 'Chọn vai trò';
$baseUrl = '/nurseborn';
?>
<!DOCTYPE html>
<html lang="vi" class="light-style customizer-hide" dir="ltr">
<?php include __DIR__ . '/fragments/head.php'; ?>
<body>
<!-- Modal chọn role -->
<div id="roleModal" class="modal fade show" tabindex="-1" aria-hidden="true" style="display: block;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn vai trò của bạn</h5>
            </div>
            <div class="modal-body text-center">
                <button class="btn btn-primary w-100 mb-2" onclick="redirectToRegister('nurse')">Tôi là Y tá</button>
                <button class="btn btn-secondary w-100" onclick="redirectToRegister('family')">Tôi là Khách Hàng</button>
            </div>
            <div class="modal-footer text-center">
                <a href="?action=login" class="btn btn-link">Quay lại đăng nhập</a>
            </div>
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/jquery/jquery.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/popper/popper.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/bootstrap.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/vendor/js/menu.js"></script>
<script src="<?php echo $baseUrl; ?>/static/assets/js/main.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>

<script>
    function redirectToRegister(role) {
        if (role === 'nurse') {
            window.location.href = '?action=register_nurse';
        } else if (role === 'family') {
            window.location.href = '?action=register_family';
        }
    }
</script>
</body>
</html>