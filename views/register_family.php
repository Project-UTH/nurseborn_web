<?php
$pageTitle = 'Đăng ký gia đình';
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
                    <div class="app-brand justify-content-center">
                        <a href="?action=home" class="app-brand-link gap-2">
                            <span class="app-brand-text demo text-body fw-bolder text-uppercase ms-4">Đăng ký</span>
                        </a>
                    </div>
                    <h4 class="mb-2">TẠO TÀI KHOẢN</h4>
                    <p class="mb-4">Điền thông tin bên dưới để bắt đầu</p>

                    <form id="formFamilyRegistration" class="mb-3" action="?action=register_family" method="POST">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và Tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required />
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>" />
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="child_name" class="form-label">Tên trẻ</label>
                            <input type="text" class="form-control" id="child_name" name="child_name" value="<?php echo isset($_POST['child_name']) ? htmlspecialchars($_POST['child_name']) : ''; ?>" />
                        </div>
                        <div class="mb-3">
                            <label for="child_age" class="form-label">Tuổi của trẻ</label>
                            <input type="text" class="form-control" id="child_age" name="child_age" value="<?php echo isset($_POST['child_age']) ? htmlspecialchars($_POST['child_age']) : ''; ?>" />
                        </div>
                        <div class="mb-3">
                            <label for="specific_needs" class="form-label">Nhu cầu cụ thể</label>
                            <textarea class="form-control" id="specific_needs" name="specific_needs"><?php echo isset($_POST['specific_needs']) ? htmlspecialchars($_POST['specific_needs']) : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="preferred_location" class="form-label">Vị trí ưu tiên</label>
                            <input type="text" class="form-control" id="preferred_location" name="preferred_location" value="<?php echo isset($_POST['preferred_location']) ? htmlspecialchars($_POST['preferred_location']) : ''; ?>" />
                        </div>
                        <input type="hidden" name="role" value="FAMILY" />
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="mb-3">
                                <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="mb-3">
                                <p style="color: green;"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
                            </div>
                        <?php endif; ?>
                        <button class="btn btn-primary d-grid w-100" type="submit">Đăng ký</button>
                    </form>

                    <p class="text-center">
                        <span>Bạn đã có tài khoản?</span>
                        <a href="?action=login"><span>Đăng nhập ngay</span></a>
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
</body>
</html>