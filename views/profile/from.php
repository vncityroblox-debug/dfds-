<?php
// Include necessary files and libraries
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
if (!@$user) {
    new Redirect('/login');
    exit;
}
$title = 'Thông tin tác giả';
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $team = isset($_POST['team']) ? $_POST['team'] : 'no';
    $teamMembers = isset($_POST['teamMembers']) ? $_POST['teamMembers'] : '';
    $otherAccount = isset($_POST['otherAccount']) ? $_POST['otherAccount'] : 'no';
    $marketAccount = isset($_POST['marketAccount']) ? $_POST['marketAccount'] : 'no';
    $workCategory = isset($_POST['workCategory']) ? implode(', ', $_POST['workCategory']) : '';

    // Prepare message
    $replacements = [
        '{team}' => $team == 'yes' ? 'Yes' : 'No',
        '{teamMembers}' => $teamMembers,
        '{otherAccount}' => $otherAccount == 'yes' ? 'Yes' : 'No',
        '{marketAccount}' => $marketAccount == 'yes' ? 'Yes' : 'No',
        '{workCategory}' => $workCategory
    ];

    // Creating message with HTML tags for bold text and <code> for placeholders
    $my_text = "<b>THÔNG TIN TÁC GIẢ</b>\n";
    $my_text .= "<b>Username:</b> <code>" . $data_user['username'] . "</code>\n"; // Username in bold
    $my_text .= "<b>Có đội không:</b> <code>{team}</code>\n"; // Placeholder wrapped with <code> tag
    $my_text .= "<b>Số thành viên trong nhóm:</b> <code>{teamMembers}</code>\n"; // Placeholder wrapped with <code> tag
    $my_text .= "<b>Có tài khoản khác không:</b> <code>{otherAccount}</code>\n"; // Placeholder wrapped with <code> tag
    $my_text .= "<b>Có tài khoản ở thị trường khác không:</b> <code>{marketAccount}</code>\n"; // Placeholder wrapped with <code> tag
    $my_text .= "<b>Hạng mục yêu thích:</b> <code>{workCategory}</code>\n"; // Placeholder wrapped with <code> tag

    // Replace placeholders with the actual values
    $my_text = str_replace(array_keys($replacements), array_values($replacements), $my_text);

    // Send message to Telegram
    sendMessAdmin($my_text);
    
    // Show alert and refresh the page
    die('<script type="text/javascript">
            if(!alert("Biểu mẫu đã được gửi, vui lòng chờ.")){
                window.location.href = window.location.href;
            }
        </script>');
}
?>



<section class="py-110">
    <div class="container">
        <div class="row">
            <div class="col-md-6 m-auto">
                <div class="settings-card">
                    <div class="settings-card-head">
                        <h4>Thông tin tác giả</h4>
                    </div>
                    <form action="" method="POST">
                        <div class="settings-card-body">
                            <div class="mb-3">
                                <label class="form-label">Bạn có đội nào không?</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="team" id="teamYes" value="yes" checked>
                                    <label class="form-check-label" for="teamYes">Đúng</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="team" id="teamNo" value="no">
                                    <label class="form-check-label" for="teamNo">KHÔNG</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="teamMembers">Nhóm của bạn có bao nhiêu thành viên?</label>
                                <select class="form-select" id="teamMembers" name="teamMembers">
                                    <option selected>Chọn một</option>
                                    <option value="1-5">1-5</option>
                                    <option value="6-10">6-10</option>
                                    <option value="11-20">11-20</option>
                                    <option value="20+">20+</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bạn có tài khoản nào khác trên nền tảng này không?</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="otherAccount" id="accountYes" value="yes">
                                    <label class="form-check-label" for="accountYes">Đúng</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="otherAccount" id="accountNo" value="no">
                                    <label class="form-check-label" for="accountNo">KHÔNG</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bạn có tài khoản ở thị trường khác không?</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marketAccount" id="marketYes" value="yes">
                                    <label class="form-check-label" for="marketYes">Đúng</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="marketAccount" id="marketNo" value="no">
                                    <label class="form-check-label" for="marketNo">KHÔNG</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bạn thích làm việc ở hạng mục nào?</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="phpCommands" name="workCategory[]" value="PHP Commands">
                                    <label class="form-check-label" for="phpCommands">Các tập lệnh PHP</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="wordpress" name="workCategory[]" value="Wordpress">
                                    <label class="form-check-label" for="wordpress">Wordpress</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="vibration" name="workCategory[]" value="Vibration">
                                    <label class="form-check-label" for="vibration">Rung động</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="http5" name="workCategory[]" value="What is HTTP5">
                                    <label class="form-check-label" for="http5">HTTP5 là gì?</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="graphics" name="workCategory[]" value="Graphics">
                                    <label class="form-check-label" for="graphics">Đồ họa</label>
                                </div>
                            </div>

                        </div>
                        <div class="settings-card-footer">
                            <div class="btn-item">
                                <button class="btn btn-primary w-100" type="submit">Nộp Đơn</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>
<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php');?>
