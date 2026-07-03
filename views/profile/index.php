<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/libs/init.php');
if (!@$user) {
    new Redirect('/login');
    exit;
}
$title = 'Thông tin tài khoản';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');
if (isset($_POST['update'])) {
    $id = $data_user['id'] ?? 0; // Lấy id user từ session

     $url_icon = $data_user['profile_picture'];
    // Xử lý ảnh đại diện nếu có upload
    if (check_img('profile_picture') == true) {
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', 4);
        $uploads_dir = '/upload/avatars/avatar_' . $rand . '.png';
        $tmp_name = $_FILES['profile_picture']['tmp_name'];
        $addlogo = move_uploaded_file($tmp_name, realpath($_SERVER["DOCUMENT_ROOT"]) . $uploads_dir);
        if ($addlogo) {
            $url_icon = $uploads_dir; // Gán giá trị cho $url_icon nếu upload thành công
        }
    }

    // Gán $url_icon vào mảng fields
    $fields = array(
        'profile_picture' => $url_icon,
        'name'        => $_POST['name'],
        'address'     => $_POST['address'],
        'skill'       => $_POST['skill'],
        'description' => $_POST['description']
    );

    // Tiến hành update vào cơ sở dữ liệu
    $db->update("users", $fields, "id = '$id'");

    // Sau khi cập nhật, bạn có thể redirect hoặc thông báo thành công
    die('<script type="text/javascript">
        if(!alert("Cập nhật thành công!")){
            window.location.href = window.location.href;
        }
    </script>');
}

if (isset($_POST['contact'])) {
    $id = $data_user['id'] ?? 0; // Lấy id user từ session hoặc từ dữ liệu người dùng
    $links = $_POST['links'] ?? [];
    
    // Chuyển mảng liên kết thành JSON
    $contactLinks = json_encode($links);


    // Cập nhật vào bảng users (sử dụng hàm update của hệ thống bạn)
    $db->update("users", ['contact_links' => $contactLinks], "id = '$id'");
    insert_log($data_user['id'], "Cập nhật ảnh đại diện");
    die('<script type="text/javascript">
        if(!alert("Lưu liên kết thành công!")){
            window.location.href = window.location.href;
        }
    </script>');
}

?>
<section class="py-110">

        <div class="container">
                <?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/navbar.php');?>
                <div class="row">
                <div class="col-lg-6">
                    <div class="settings-card">
                        <div class="settings-card-head">
                            <h4>THÔNG TIN TÀI KHOẢN</h4>
                        </div>
                        <div class="settings-card-body">
                            <form method="POST" action="" enctype="multipart/form-data" class="row g-4">
                                <div class="col-md-12">
                                    <div>
                                        <label for="profile_picture" class="form-label">Chọn ảnh đại
                                            diện mới</label>
                                        <input type="file" class="form-control shadow-none"
                                            id="profile_picture" name="profile_picture" accept="image/*">
                                        <i>Chỉ cho phép các định dạng như: jpeg,png,gif. Kích thước ảnh
                                            tối đa 2MB</i>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div>
                                        <label for="fname" class="form-label">Tài khoản</label>
                                        <input type="text" class="form-control shadow-none"
                                            value="<?=$data_user['username'] ?? null ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div>
                                        <label for="fname" class="form-label">Họ và tên</label>
                                        <input type="text" class="form-control shadow-none"
                                            value="<?=$data_user['name'] ?? null ?>" name="name">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div>
                                        <label for="fname" class="form-label">Email</label>
                                        <input type="text" class="form-control shadow-none"
                                            value="<?=$data_user['email'] ?? null ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <label for="fname" class="form-label">Loại tài khoản</label>
                                        <input type="text" class="form-control shadow-none"
                                            value="<?= $data_user['provider'] == "google" ? 'GOOGLE' : 'Tài khoản' ?? null ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <label for="fname" class="form-label">Loại cấp bậc</label>
                                        <input type="text" class="form-control shadow-none"
                                            value="<?=getUserRank($data_user['id']) ?? 0 ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <label for="fname" class="form-label">Ngày đăng ký</label>
                                        <input type="text" class="form-control shadow-none"
                                            value="<?=$data_user['create_date'] ?? null ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <label for="fname" class="form-label">Hoạt động gần đây</label>
                                        <input type="text" class="form-control shadow-none"
                                            value="<?= isset($data_user['time_session']) ? date('Y/m/d H:i:s', $data_user['time_session']) : '' ?>"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label for="fname" class="form-label">Địa chỉ</label>
                                        <input type="text" class="form-control shadow-none"
                                            value="<?=$data_user['address'] ?? null ?>" name="address" placeholder="Địa chỉ">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label for="fname" class="form-label">Kỹ năng</label>
                                        <input type="text" class="form-control shadow-none"
                                            value="<?=$data_user['skill'] ?? null ?>" name="skill"
                                            placeholder="Mỗi kỹ năng cách nhau bởi dấu phẩy">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div>
                                        <label for="fname" class="form-label">Mô ta về bản thân</label>
                                        <textarea type="text" name="description" class="form-control shadow-none" rows="5"
                                            placeholder="Mô tả ngắn"><?=$data_user['description'] ?? null ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="update" class="btn btn-primary">
                                        Cập Nhật
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
    <div class="settings-card">
        <div class="settings-card-head">
            <h4>THÔNG TIN LIÊN HỆ</h4>
        </div>
        <?php
// Giả sử bạn đã có dữ liệu user từ CSDL và gán vào biến $data_user

// Xử lý dữ liệu liên kết đã lưu
$saved_links = [];
if (isset($data_user['contact_links']) && !empty($data_user['contact_links'])) {
    $saved_links = json_decode($data_user['contact_links'], true);
}
?>
        <div class="settings-card-body">
            <form id="contact-links-form" action="" method="POST">
                <div id="link-container">
                    <?php if (!empty($saved_links)) {
                        $index = 0;
                        foreach ($saved_links as $link) { ?>
                            <div class="row g-2 mb-2 link-row">
                                <div class="col-md-4">
                                    <select class="form-select" name="links[<?= $index ?>][platform]" required>
                                        <option value="" disabled <?= empty($link['platform']) ? 'selected' : '' ?>>Chọn nền tảng</option>
                                        <option value="zalo" <?= ($link['platform'] == 'zalo') ? 'selected' : '' ?>>Zalo</option>
                                        <option value="facebook" <?= ($link['platform'] == 'facebook') ? 'selected' : '' ?>>Facebook</option>
                                        <option value="telegram" <?= ($link['platform'] == 'telegram') ? 'selected' : '' ?>>Telegram</option>
                                        <option value="youtube" <?= ($link['platform'] == 'youtube') ? 'selected' : '' ?>>YouTube</option>
                                        <option value="instagram" <?= ($link['platform'] == 'instagram') ? 'selected' : '' ?>>Instagram</option>
                                        <option value="twitter" <?= ($link['platform'] == 'twitter') ? 'selected' : '' ?>>Twitter</option>
                                        <option value="tiktok" <?= ($link['platform'] == 'tiktok') ? 'selected' : '' ?>>TikTok</option>
                                        <option value="linkedin" <?= ($link['platform'] == 'linkedin') ? 'selected' : '' ?>>LinkedIn</option>
                                        <option value="pinterest" <?= ($link['platform'] == 'pinterest') ? 'selected' : '' ?>>Pinterest</option>
                                        <option value="snapchat" <?= ($link['platform'] == 'snapchat') ? 'selected' : '' ?>>Snapchat</option>
                                        <option value="reddit" <?= ($link['platform'] == 'reddit') ? 'selected' : '' ?>>Reddit</option>
                                        <option value="discord" <?= ($link['platform'] == 'discord') ? 'selected' : '' ?>>Discord</option>
                                        <option value="custom" <?= ($link['platform'] == 'custom') ? 'selected' : '' ?>>Khác</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="url" class="form-control" name="links[<?= $index ?>][url]" placeholder="https://example.com" required value="<?= htmlspecialchars($link['url']) ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger w-100 remove-link" onclick="removeLink(this)">Xóa</button>
                                </div>
                            </div>
                        <?php 
                        $index++;
                        }
                    } ?>
                </div>
                <button type="button" id="add-link" class="btn btn-success mt-3">Thêm dòng</button>
                <button type="submit" name="contact" class="btn btn-primary mt-3">Lưu liên kết</button>
            </form>
        </div>
    </div>
</div>
    </section>
</main>
<script>
    // Nếu có các liên kết đã lưu, linkIndex bằng số lượng đã lưu, ngược lại mặc định là 1
    let linkIndex = <?php echo !empty($saved_links) ? count($saved_links) : 1; ?>;

    document.getElementById('add-link').addEventListener('click', function() {
        const container = document.getElementById('link-container');

        const newRow = document.createElement('div');
        newRow.classList.add('row', 'g-2', 'mb-2', 'link-row');
        newRow.innerHTML = `
            <div class="col-md-4">
                <select class="form-select" name="links[${linkIndex}][platform]" required>
                    <option value="" disabled selected>Chọn nền tảng</option>
                    <option value="zalo">Zalo</option>
                    <option value="facebook">Facebook</option>
                    <option value="telegram">Telegram</option>
                    <option value="youtube">YouTube</option>
                    <option value="instagram">Instagram</option>
                    <option value="twitter">Twitter</option>
                    <option value="tiktok">TikTok</option>
                    <option value="linkedin">LinkedIn</option>
                    <option value="pinterest">Pinterest</option>
                    <option value="snapchat">Snapchat</option>
                    <option value="reddit">Reddit</option>
                    <option value="discord">Discord</option>
                    <option value="custom">Khác</option>
                </select>
            </div>
            <div class="col-md-6">
                <input type="url" class="form-control" name="links[${linkIndex}][url]" placeholder="https://example.com" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger w-100 remove-link" onclick="removeLink(this)">Xóa</button>
            </div>
        `;
        container.appendChild(newRow);
        linkIndex++;
    });

    function removeLink(button) {
        const row = button.closest('.link-row');
        row.remove();
    }
</script>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>