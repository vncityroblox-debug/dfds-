<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');

$title = 'Tài liệu API - ' . $db->site('title');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');
?>

<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f5f7fa; /* Giữ nguyên background */
    }
    .api-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .token-note-section {
        margin-bottom: 30px;
    }
    .token-note-section .card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background-color: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .token-note-section .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    .token-note-section .card-header {
        background-color: transparent;
        border-bottom: none;
        padding: 20px 20px 10px;
    }
    .token-note-section .card-header h2 {
        font-size: 20px;
        color: #1a202c;
        margin-bottom: 5px;
    }
    .token-note-section .card-header span {
        font-size: 14px;
        color: #718096;
    }
    .token-note-section .card-body {
        padding: 0 20px 20px;
    }
    .token-note-section .form-control {
        border-radius: 8px;
        background-color: #f7fafc;
        font-size: 14px;
        color: #2d3748;
    }
    .token-note-section .btn-primary {
        border-radius: 8px;
        background-color: #007bff;
        border: none;
        padding: 8px 12px;
    }
    .token-note-section .btn-primary:hover {
        background-color: #0056b3;
    }
    .token-note-section .txt-danger {
        color: #e53e3e;
        font-weight: 500;
    }
    .tab-menu {
        display: flex;
        gap: 12px;
        margin-bottom: 30px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e2e8f0;
    }
    .tab-button {
        padding: 10px 24px;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 500;
        color: #4a5568;
        transition: all 0.3s ease;
    }
    .tab-button.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    .tab-button[disabled] {
        background-color: #edf2f7;
        border-color: #edf2f7;
        color: #a0aec0;
        cursor: not-allowed;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .api-section {
        margin-bottom: 30px;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .api-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    .api-section .row {
        margin: 0;
    }
    .api-section .col-md-6 {
        padding: 24px;
    }
    .api-section .col-md-6:first-child {
        border-right: 1px solid #e2e8f0;
    }
    .api-section h2 {
        font-size: 20px;
        margin-bottom: 12px;
        color: #1a202c;
    }
    .api-section h3 {
        font-size: 16px;
        color: #2d3748;
        margin: 12px 0;
    }
    .api-section p {
        margin: 6px 0;
        color: #4a5568;
        font-size: 14px;
    }
    .api-section code {
        background-color: #f7fafc;
        padding: 3px 6px;
        border-radius: 4px;
        color: #e53e3e;
        font-size: 14px;
    }
    .api-section table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    .api-section th, .api-section td {
        padding: 12px;
        border: 1px solid #e2e8f0;
        text-align: left;
        font-size: 14px;
    }
    .api-section th {
        background-color: #f7fafc;
        font-weight: 600;
        color: #2d3748;
    }
    .api-section td {
        color: #4a5568;
    }
    .response-box {
        background-color: #1a202c;
        padding: 16px;
        border-radius: 8px;
        color: #e2e8f0;
    }
    .response-box h3 {
        font-size: 14px;
        margin-bottom: 10px;
        color: #a0aec0;
        font-weight: 500;
    }
    .response-box pre {
        margin: 0;
        white-space: pre-wrap;
        color: #e2e8f0;
        font-size: 13px;
    }
    .response-box code {
        background: none;
        padding: 0;
        color: inherit;
    }
    details {
        margin-bottom: 12px;
    }
    summary {
        cursor: pointer;
        font-weight: 600;
        padding: 12px;
        background-color: #f7fafc;
        border-radius: 8px;
        color: #2d3748;
        transition: background-color 0.2s ease;
    }
    summary:hover {
        background-color: #edf2f7;
    }
</style>


    <div class="container">
        <div class="row">
            <div class="col-md-12 col-12">
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                     
                    </ol>
                </nav>
                <h2 class="breadcrumb-title">
                    Tài liệu API
                </h2>
            </div>
        </div>
    </div>
</div>

<section class="api-documentation">
    <div class="api-container">
        <div class="token-note-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2>Thông Tin Tích Hợp API</h2>
                            <span>Vui Lòng Không Để Lộ APIKEY Tránh Mất Tiền.</span>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="input-group mb-2">
                                    <?php if(@$user): ?>
                                        <div class="form-control" id="apikey" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; display: inline-block;"><?= $data_user['token']; ?></div>
                                        <button class="btn btn-primary" onclick="copyText('<?= $data_user['token']; ?>');">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    <?php else: ?>
                                        <div class="form-control" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; display: inline-block;">Đăng Nhập Đi Cu</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if(@$user): ?>
                                <b class="txt-danger">💡 Vui Lòng Không Để Lộ APIKEY Tránh Dẫn Đến Mất Tiền.</b>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2>Ghi Chú</h2>
                            <span>Một Số Lưu Ý Và Hướng Dẫn Nhanh, Bạn Nên Đọc!</span>
                        </div>
                        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<div class="card-body" style="font-size: 16px;">
    <p>
        <i class="fas fa-code text-primary"></i> Bạn không rành lập trình? Đừng lo!<br>
        <a href="https://cmstdev.co/product/code-cho-thue-cronjob-api-sang-cmstdev-co" 
           class="btn btn-success mt-2 mb-3" target="_blank">
            <i class="fas fa-download"></i> Tải miễn phí mã nguồn tích hợp sẵn API
        </a>
    </p>
    <p>
        <i class="fab fa-telegram text-info"></i>
        <strong> Hỗ trợ nhanh qua Telegram: </strong> 
        <a href="https://t.me/cmstdev_vn" class="text-decoration-none text-primary" target="_blank">
            @cmstdev_vn <i class="fas fa-arrow-right"></i>
        </a>
    </p>
</div>

                    </div>
                </div>
            </div>
        </div>

        <div class="tab-menu">
            <button class="tab-button active" onclick="showTab('cron')">Tài Liệu Cron</button>
            <button class="tab-button" disabled>Mã Nguồn</button>
            <button class="tab-button" disabled>Cloud VPS</button>
            <button class="tab-button" disabled>Hosting</button>
        </div>

        <div id="cron" class="tab-content active">
            <!-- API Lấy Thông Tin Tài Khoản -->
            <div class="api-section">
                <div class="row">
                    <div class="col-md-6">
                        <h2>API Lấy Thông Tin Tài Khoản</h2>
                        <p><strong>Endpoint:</strong> <code>https://cmstdev.co/api/cron/profile?token={Token_API}</code></p>
                        <p><strong>Method:</strong> GET</p>
                        <h3>Query Parameters</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tham số</th>
                                    <th>Kiểu dữ liệu</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>token</td>
                                    <td>String</td>
                                    <td>Token API</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="response-box">
                            <h3>Response</h3>
                            <pre><code>
{
  "status": "success",
  "message": "Thành công",
  "data": {
    "username": "DoiLacLoi",
    "email": "minhphsdepchai@gmail.com",
    "coin": "7379150",
    "chietkhau": "0"
  }
}
                            </code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Lấy Thông Tin Máy Chủ Cronjob -->
            <div class="api-section">
                <div class="row">
                    <div class="col-md-6">
                        <h2>API Lấy Thông Tin Máy Chủ Cronjob</h2>
                        <p><strong>Endpoint:</strong> <code>https://cmstdev.co/api/cron/server?token={Token_API}</code></p>
                        <p><strong>Method:</strong> GET</p>
                        <h3>Query Parameters</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tham số</th>
                                    <th>Kiểu dữ liệu</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>token</td>
                                    <td>String</td>
                                    <td>Token API</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="response-box">
                            <h3>Response</h3>
                            <pre><code>
{
  "status": "success",
  "message": "Thành công",
  "data": [
    {
      "id": "2",
      "name": "Siêu Rẻ V1",
      "price": 200,
      "quantity": 92,
      "limit_second": 1
    },
    {
      "id": "3",
      "name": "Siêu Vip V2",
      "price": 500,
      "quantity": 450,
      "limit_second": 1
    },
    {
      "id": "4",
      "name": "Ổn Định V3",
      "price": 1000,
      "quantity": 47,
      "limit_second": 1
    }
  ]
}
                            </code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Tạo Đơn Cron -->
            <div class="api-section">
                <div class="row">
                    <div class="col-md-6">
                        <h2>API Tạo Đơn Cron</h2>
                        <p><strong>Endpoint:</strong> <code>https://cmstdev.co/api/cron/create</code></p>
                        <p><strong>Method:</strong> POST</p>
                        <h3>Form Data</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tham số</th>
                                    <th>Kiểu dữ liệu</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>linkcron</td>
                                    <td>String</td>
                                    <td>Link cần cron</td>
                                </tr>
                                <tr>
                                    <td>timeloop</td>
                                    <td>Int</td>
                                    <td>Số giây chạy</td>
                                </tr>
                                <tr>
                                    <td>maychucron</td>
                                    <td>Int</td>
                                    <td>Id máy chủ cron</td>
                                </tr>
                                <tr>
                                    <td>thoigiangiahan</td>
                                    <td>Int</td>
                                    <td>Số tháng cần thuê</td>
                                </tr>
                                <tr>
                                    <td>token</td>
                                    <td>String</td>
                                    <td>Token API</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="response-box">
                            <h3>Response</h3>
                            <pre><code>
{
    "status": "success",
    "msg": "Thanh toán đơn hàng thành công",
    "data": {
        "trans_id": "dsajdaUdsn8",
        "url": "https://cmstdev.vn",
        "second": "5",
        "price": 50000,
        "status": "hoatdong",
        "created_at": "2024/02/04 22:28:12",
        "expired_date": "2024/07/03 22:28:12",
        "expired_timestamp": 1720020492
    }
}
                            </code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Lấy Lịch Sử Giao Dịch -->
            <div class="api-section">
                <div class="row">
                    <div class="col-md-6">
                        <h2>API Lấy Lịch Sử Giao Dịch</h2>
                        <p><strong>Endpoint:</strong> <code>https://cmstdev.co/api/cron/history?token={Token_API}&limit={Số lượng giao dịch}</code></p>
                        <p><strong>Method:</strong> GET</p>
                        <h3>Query Parameters</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tham số</th>
                                    <th>Kiểu dữ liệu</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>token</td>
                                    <td>String</td>
                                    <td>Token API</td>
                                </tr>
                                <tr>
                                    <td>limit</td>
                                    <td>Int</td>
                                    <td>Số lượng giao dịch</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="response-box">
                            <h3>Response</h3>
                            <pre><code>
{
  "status": "success",
  "message": "Thành công",
  "data": [
    {
      "trans_id": "6",
      "url": "https://cmstdev.co/cron/deposit/check?type=ACB",
      "server": "2",
      "second": "30",
      "status": "active",
      "response": "200",
      "created_at": "2025-05-17 13:34:53",
      "expired_date": "2026-05-17 13:34:53",
      "expired_timestamp": 1778999693,
      "last_run": "2025-05-18 16:40:04",
      "lastrun_timestamp": 1747561204
    },
    {
      "trans_id": "5",
      "url": "https://server.cmstdev.vn/cron/deposit/check?type=ACB",
      "server": "2",
      "second": "30",
      "status": "active",
      "response": "200",
      "created_at": "2025-05-17 13:33:06",
      "expired_date": "2026-05-17 13:33:06",
      "expired_timestamp": 1778999586,
      "last_run": "2025-05-18 16:40:04",
      "lastrun_timestamp": 1747561204
    }
  ]
}
                            </code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Lấy Chi Tiết Giao Dịch -->
            <div class="api-section">
                <div class="row">
                    <div class="col-md-6">
                        <h2>API Lấy Chi Tiết Giao Dịch</h2>
                        <p><strong>Endpoint:</strong> <code>https://cmstdev.co/api/cron/checkhistory?token={Token_API}&trans_id={Mã đơn hàng}</code></p>
                        <p><strong>Method:</strong> GET</p>
                        <h3>Query Parameters</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tham số</th>
                                    <th>Kiểu dữ liệu</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>token</td>
                                    <td>String</td>
                                    <td>Token API</td>
                                </tr>
                                <tr>
                                    <td>trans_id</td>
                                    <td>String</td>
                                    <td>Mã đơn hàng</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="response-box">
                            <h3>Response</h3>
                            <pre><code>
{
  "status": "success",
  "message": "Thành công",
  "data": [
    {
      "trans_id": "1",
      "url": "https://smmlight.net/schedule/orders/update-order?minute=5",
      "server": "4",
      "second": "10",
      "status": "active",
      "response": "200",
      "created_at": "2025-05-09 00:30:53",
      "expired_date": "2026-05-09 00:30:53",
      "expired_timestamp": 1778261453,
      "last_run": "2025-05-18 16:41:16",
      "lastrun_timestamp": 1747561276
    }
  ]
}
                            </code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Thực Hiện Hành Động Cron -->
            <div class="api-section">
                <div class="row">
                    <div class="col-md-6">
                        <h2>API Thực Hiện Hành Động Cron</h2>
                        <p><strong>Endpoint:</strong> <code>https://cmstdev.co/api/cron/action</code></p>
                        <p><strong>Method:</strong> POST</p>
                        <p>API này hỗ trợ nhiều hành động khác nhau. Nhấn vào từng hành động dưới đây để xem chi tiết:</p>

                        <details>
                            <summary>Action: Chỉnh sửa (edit)</summary>
                            <div>
                                <h4>Form Data</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Tham số</th>
                                            <th>Kiểu dữ liệu</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>action</td>
                                            <td>String</td>
                                            <td>"edit"</td>
                                        </tr>
                                        <tr>
                                            <td>url</td>
                                            <td>String</td>
                                            <td>Link cron mới hoặc cũ</td>
                                        </tr>
                                        <tr>
                                            <td>second</td>
                                            <td>Int</td>
                                            <td>Số giây chạy</td>
                                        </tr>
                                        <tr>
                                            <td>trans_id</td>
                                            <td>String</td>
                                            <td>Mã đơn hàng</td>
                                        </tr>
                                        <tr>
                                            <td>token</td>
                                            <td>String</td>
                                            <td>Token API</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </details>

                        <details>
                            <summary>Action: Chạy lại (active)</summary>
                            <div>
                                <h4>Form Data</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Tham số</th>
                                            <th>Kiểu dữ liệu</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>action</td>
                                            <td>String</td>
                                            <td>"active"</td>
                                        </tr>
                                        <tr>
                                            <td>trans_id</td>
                                            <td>String</td>
                                            <td>Mã đơn hàng</td>
                                        </tr>
                                        <tr>
                                            <td>token</td>
                                            <td>String</td>
                                            <td>Token API</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </details>

                        <details>
                            <summary>Action: Tạm dừng (stop)</summary>
                            <div>
                                <h4>Form Data</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Tham số</th>
                                            <th>Kiểu dữ liệu</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>action</td>
                                            <td>String</td>
                                            <td>"stop"</td>
                                        </tr>
                                        <tr>
                                            <td>trans_id</td>
                                            <td>String</td>
                                            <td>Mã đơn hàng</td>
                                        </tr>
                                        <tr>
                                            <td>token</td>
                                            <td>String</td>
                                            <td>Token API</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </details>

                        <details>
                            <summary>Action: Gia hạn (giahan)</summary>
                            <div>
                                <h4>Form Data</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Tham số</th>
                                            <th>Kiểu dữ liệu</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>action</td>
                                            <td>String</td>
                                            <td>"giahan"</td>
                                        </tr>
                                        <tr>
                                            <td>month</td>
                                            <td>Int</td>
                                            <td>Số tháng cần gia hạn</td>
                                        </tr>
                                        <tr>
                                            <td>trans_id</td>
                                            <td>String</td>
                                            <td>Mã đơn hàng</td>
                                        </tr>
                                        <tr>
                                            <td>token</td>
                                            <td>String</td>
                                            <td>Token API</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </details>
                    </div>
                    <div class="col-md-6">
                        <div class="response-box">
                            <h3>Response (Chỉnh sửa)</h3>
                            <pre><code>
{
    "status": "success",
    "msg": "Cập nhật thành công"
}
                            </code></pre>
                        </div>
                        <div class="response-box" style="margin-top: 12px;">
                            <h3>Response (Chạy lại)</h3>
                            <pre><code>
{
    "status": "success",
    "msg": "Đã kích hoạt thành công"
}
                            </code></pre>
                        </div>
                        <div class="response-box" style="margin-top: 12px;">
                            <h3>Response (Tạm dừng)</h3>
                            <pre><code>
{
    "status": "success",
    "msg": "Đã dừng cron thành công"
}
                            </code></pre>
                        </div>
                        <div class="response-box" style="margin-top: 12px;">
                            <h3>Response (Gia hạn)</h3>
                            <pre><code>
{
    "status": "success",
    "msg": "Gia hạn thành công",
    "expired_date": "2024/11/30 22:28:11",
    "expired_timestamp": 1732980491
}
                            </code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="source" class="tab-content">
            <div class="api-section">
                <h2>Mã Nguồn</h2>
                <p>Sắp Ra Mắt</p>
            </div>
        </div>

        <div id="vps" class="tab-content">
            <div class="api-section">
                <h2>Cloud VPS</h2>
                <p>Sắp Ra Mắt</p>
            </div>
        </div>

        <div id="hosting" class="tab-content">
            <div class="api-section">
                <h2>Hosting</h2>
                <p>Sắp Ra Mắt</p>
            </div>
        </div>
    </div>
</section>

<script>
    function copyText(text) {
        navigator.clipboard.writeText(text)
            .then(() => {
                alert('Text copied to clipboard!');
            })
            .catch(err => {
                console.error('Failed to copy: ', err);
                alert('Failed to copy text.');
            });
    }

    function showTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(function(content) {
            content.classList.remove('active');
        });
        document.getElementById(tabId).classList.add('active');
        document.querySelectorAll('.tab-button').forEach(function(button) {
            button.classList.remove('active');
        });
        document.querySelector(`.tab-button[onclick="showTab('${tabId}')"]`).classList.add('active');
    }
</script>

<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>