<?php
$pageTitle = 'Đăng ký y tá';
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
                    <h4 class="mb-2">ĐĂNG KÝ TÀI KHOẢN Y TÁ</h4>
                    <p class="mb-4">Nhập thông tin của bạn để tham gia hệ thống</p>

                    <form id="registerNurseForm" class="mb-3" action="?action=register_nurse" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required />
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Khu vực làm việc</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Tiểu sử (Bio)</label>
                            <textarea class="form-control" id="bio" name="bio"><?php echo isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Ảnh đại diện</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" />
                        </div>
                        <div class="mb-3">
                            <label for="skills" class="form-label">Kỹ năng chuyên môn</label>
                            <textarea class="form-control" id="skills" name="skills" required><?php echo isset($_POST['skills']) ? htmlspecialchars($_POST['skills']) : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="experience_years" class="form-label">Số năm kinh nghiệm</label>
                            <input type="number" class="form-control" id="experience_years" name="experience_years" value="<?php echo isset($_POST['experience_years']) ? htmlspecialchars($_POST['experience_years']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="hourly_rate" class="form-label">Mức giá theo giờ (VNĐ)</label>
                            <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" value="<?php echo isset($_POST['hourly_rate']) ? htmlspecialchars($_POST['hourly_rate']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="daily_rate" class="form-label">Mức giá theo ngày (VNĐ)</label>
                            <input type="number" class="form-control" id="daily_rate" name="daily_rate" value="<?php echo isset($_POST['daily_rate']) ? htmlspecialchars($_POST['daily_rate']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="weekly_rate" class="form-label">Mức giá theo tuần (VNĐ)</label>
                            <input type="number" class="form-control" id="weekly_rate" name="weekly_rate" value="<?php echo isset($_POST['weekly_rate']) ? htmlspecialchars($_POST['weekly_rate']) : ''; ?>" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chứng chỉ và bằng cấp</label>
                            <div id="certificates-section">
                                <div class="certificate-item mb-2">
                                    <input type="text" class="form-control mb-1" name="certificate_names[]" placeholder="Tên chứng chỉ" value="<?php echo isset($_POST['certificate_names'][0]) ? htmlspecialchars($_POST['certificate_names'][0]) : ''; ?>" />
                                    <input type="file" class="form-control" name="certificates[]" accept=".pdf,.jpg,.jpeg,.png" />
                                    <button type="button" class="btn btn-danger remove-certificate mt-1">Xóa</button>
                                </div>
                            </div>
                            <button type="button" id="add-certificate" class="btn btn-secondary mt-2">Thêm chứng chỉ</button>
                        </div>
                        <input type="hidden" name="role" value="NURSE" />
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
                        <button type="submit" class="btn btn-primary d-grid w-100">Đăng ký</button>
                    </form>

                    <p class="text-center">
                        <span>Đã có tài khoản?</span>
                        <a href="?action=login">Đăng nhập ngay</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript để xử lý thêm/xóa chứng chỉ -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const addCertificateButton = document.getElementById("add-certificate");
        if (addCertificateButton) {
            addCertificateButton.addEventListener("click", function() {
                let container = document.getElementById("certificates-section");
                if (container) {
                    let newItem = document.createElement("div");
                    newItem.classList.add("certificate-item", "mb-2");
                    newItem.innerHTML = `
                        <input type="text" class="form-control mb-1" name="certificate_names[]" placeholder="Tên chứng chỉ" />
                        <input type="file" class="form-control" name="certificates[]" accept=".pdf,.jpg,.jpeg,.png" />
                        <button type="button" class="btn btn-danger remove-certificate mt-1">Xóa</button>
                    `;
                    container.appendChild(newItem);
                } else {
                    console.error("Không tìm thấy certificates-section");
                }
            });
        } else {
            console.error("Không tìm thấy add-certificate button");
        }

        const certificatesSection = document.getElementById("certificates-section");
        if (certificatesSection) {
            certificatesSection.addEventListener("click", function(event) {
                if (event.target.classList.contains("remove-certificate")) {
                    event.target.parentElement.remove();
                }
            });
        } else {
            console.error("Không tìm thấy certificates-section");
        }

        const form = document.getElementById("registerNurseForm");
        if (form) {
            form.addEventListener("submit", function(event) {
                console.log("Form đang được gửi...");
                const roleField = document.querySelector("input[name='role']");
                if (roleField) {
                    console.log("Role:", roleField.value);
                } else {
                    console.error("Không tìm thấy trường role trong form");
                }
            });
        } else {
            console.error("Không tìm thấy registerNurseForm");
        }
    });
</script>

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