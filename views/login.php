<?php
$pageTitle = 'Đăng nhập';
$baseUrl = '/nurseborn';
?>
<!DOCTYPE html>
<html lang="vi" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $baseUrl; ?>/static/assets/" data-template="vertical-menu-template-free">
<?php include __DIR__ . '/fragments/head.php'; ?>
<body>
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card">
                <div class="card-body">
                    <div class="app-brand demo">
                        <a href="?action=home" class="app-brand-link d-flex align-items-center">
                            <img src="<?php echo $baseUrl; ?>/static/assets/img/favicon/favicon.png" alt="Logo" width="30" height="30">
                            <span class="app-brand-text demo text-body fw-bolder text-uppercase ms-2">NURSEBORN</span>
                        </a>
                    </div>
                    <h4 class="mb-2">Chào mừng đến với NurseBorn! 👋</h4>
                    <p class="mb-4">Hãy đăng nhập để bắt đầu</p>

                    <form id="formAuthentication" class="mb-3" action="?action=login" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" autofocus required />
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Mật khẩu</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password" placeholder="············" aria-describedby="password" required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="mb-3">
                                <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_GET['logout'])): ?>
                            <div class="mb-3">
                                <p style="color: green;">Đăng xuất thành công</p>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Đăng nhập</button>
                        </div>
                    </form>

                    <p class="text-center">
                        <span>Bạn chưa có tài khoản?</span>
                        <a href="?action=role_selection">
                            <span>Tạo tài khoản</span>
                        </a>
                    </p>
                </div>
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

<!-- Kiểm tra gửi form -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("formAuthentication");
        if (form) {
            form.addEventListener("submit", function(event) {
                console.log("Form đăng nhập đang được gửi...");
                console.log("Username:", document.querySelector("input[name='username']").value);
                console.log("Password:", document.querySelector("input[name='password']").value);
            });
        } else {
            console.error("Không tìm thấy formAuthentication");
        }
    });
</script>
</body>
</html>