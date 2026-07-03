<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php');
if ($db->site('vps_status') != 1) {
    new Redirect('/');
    exit;
}

if (isset($_GET['id'])) {
    $id = Anti_xss($_GET['id']);
    $row = $db->get_row("SELECT * FROM `tbl_cloudvps` WHERE `id` = '{$id}' AND `status` = 1");

    if (!$row) {
        new Redirect('/');
    }

    $detail = json_decode($row['detail'], true);
    $price = json_decode($row['price'], true);

    switch ($row['site']) {
        case 'VNCLOUD':
            $os = json_decode($db->site('os_vps'), true);
            break;
        case 'CLOUDNEST':
            $os = json_decode($db->site('os_vps_cloudnest'), true);
            break;
        case 'H2CLOUD':
            $os = json_decode($db->site('os_vps_h2cloud'), true);
            break;
        default:
            $os = [];
            break;
    }

    $ck = @$user ? $data_user['chietkhau'] : 0;
    
    $title = "Đăng ký dịch vụ VPS - " . $row['name'];
} else {
    new Redirect('/');
}

require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/header.php');
require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/sidebar.php');

?>
 <div class="w-breadcrumb-area">
        <div class="breadcrumb-img">
            <div class="breadcrumb-left">
                <img src="/assets/images/banner-bg-03.png" alt="img">
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/">Trang chủ</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Cloud VPS</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">
                        Đăng ký dịch vụ <?= $row['name'] ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <section class="py-110">
        <div class="container">

            <div class="row">
                <div class="col-md-8">
                    <div class="shadow-sm p-2">
                        <div class="row">
                            <label for="username" class="form-label w-100">Mua thêm</label>

                            <!-- CPU Section -->
                            <div class="col-12 col-sm-4 mb-1 text-center">
                                <label class="d-block">CPU (1ĐV = 1Core)</label>
                                <div class="touchspin-wrapper d-flex align-items-center justify-content-center">
                                    <button class="decrement-touchspin btn btn-primary px-2 py-1 rounded"><i class='bx bx-minus'></i></button>
                                    <input class="input-touchspin form-control text-center border-primary mx-1" id="cpu" type="number" value="0" onkeyup="totalPayment()" readonly>
                                    <button class="increment-touchspin btn btn-primary px-2 py-1 rounded"><i class='bx bx-plus'></i></button>
                                </div>
                            </div>

                            <!-- RAM Section -->
                            <div class="col-12 col-sm-4 mb-1 text-center">
                                <label class="d-block">RAM (1ĐV = 1GB)</label>
                                <div class="touchspin-wrapper d-flex align-items-center justify-content-center">
                                    <button class="decrement-touchspin btn btn-success px-2 py-1 rounded"><i class='bx bx-minus'></i></button>
                                    <input class="input-touchspin form-control text-center border-success mx-1" id="ram" type="number" value="0" onkeyup="totalPayment()" readonly>
                                    <button class="increment-touchspin btn btn-success px-2 py-1 rounded"><i class='bx bx-plus'></i></button>
                                </div>
                            </div>

                            <!-- DISK Section -->
                            <div class="col-12 col-sm-4 mb-1 text-center">
                                <label class="d-block">DISK (1ĐV = 10GB)</label>
                                <div class="touchspin-wrapper d-flex align-items-center justify-content-center">
                                    <button class="decrement-touchspin btn btn-danger px-2 py-1 rounded"><i class='bx bx-minus'></i></button>
                                    <input class="input-touchspin form-control text-center border-danger mx-1" id="disk" type="number" value="0" onkeyup="totalPayment()" readonly>
                                    <button class="increment-touchspin btn btn-danger px-2 py-1 rounded"><i class='bx bx-plus'></i></button>
                                </div>
                            </div>

                            <label for="username" class="form-label mt-3 w-full">Thời gian</label>
                            <?php 
                            foreach ($price as $key => $dataprice) { 
                                $isActive = ($key == "monthly") ? "active-select" : "";
                                $cycle = getDurationMappingValue($key);
                                $discountedPrice = $dataprice['amount'] - $dataprice['amount'] * $ck / 100;
                            ?>
                                <div class="col-6 col-sm-6 col-md-4 mb-2">
                                    <div role="button" 
                                         id="item<?php echo htmlspecialchars($key); ?>" 
                                         data-duration="<?php echo htmlspecialchars($key); ?>" 
                                         data-price="<?php echo htmlspecialchars($dataprice['amount']); ?>" 
                                         data-cycle="<?php echo htmlspecialchars($cycle); ?>" 
                                         class="card-wrapper rounded h-100 item border p-2 text-center <?php echo $isActive; ?> mx-1">
                                        <h6><?php echo htmlspecialchars($dataprice['billing_cycle']); ?></h6>
                                        <p class="text-red-500 font-bold">Giá <?php echo format_cash($discountedPrice); ?>đ</p>
                                    </div>
                                </div>
                            <?php  }  ?>
                            
                            <label for="os" class="form-label mt-3 w-full">Hệ điều hành</label>
                            <div class="row">
                                <?php foreach ($os['os-vps'] as $key => $dataos) { ?>
                                <div class="col-6 col-sm-6 col-md-4 mb-2">
                                    <div role="button" class="card-wrapper border rounded os p-3 text-center" data-osid="<?php echo htmlspecialchars($dataos['os-id']); ?>" id="os<?php echo htmlspecialchars($dataos['os-id']); ?>">
                                        <div class="d-flex justify-content-center align-items-center mb-2">
                                            <img src="<?php echo htmlspecialchars(getImageSource($dataos['os-name'])); ?>" alt="<?php echo htmlspecialchars($dataos['os-name']); ?>" width="50">
                                        </div>
                                        <h6><?php echo htmlspecialchars($dataos['os-name']); ?></h6>
                                    </div>
                                </div>
                            <?php } ?>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-4">
                    <div class="shadow-sm rounded-lg p-2 sticky-top" style="top: 20px;">
                        <div class="card-header">
                            <h4 class="h5 font-semibold">Thông Tin Thanh Toán</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-1">GÓI: <?= $row['name'] ?></p>

                            <div class="d-flex justify-content-between mb-1">
                                <span>Chu Kỳ Thanh Toán</span>
                                <span class="text-danger" id="cycle">1 Tháng</span>
                            </div>

                            <div class="d-flex justify-content-between mb-1">
                                <span>CPU (Mua Thêm)</span>
                                <span class="text-danger" id="totalcpu">0 Core</span>
                            </div>

                            <div class="d-flex justify-content-between mb-1">
                                <span>Ram (Mua Thêm)</span>
                                <span class="text-danger" id="totalram">0 GB</span>
                            </div>

                            <div class="d-flex justify-content-between mb-1">
                                <span>Disk (Mua Thêm)</span>
                                <span class="text-danger" id="totaldisk">0 GB</span>
                            </div>

                            <label class="mt-3 mb-1">Mã giảm giá</label>
                            <input type="text" class="form-control mb-3" id="coupon" onchange="totalPayment()" onkeyup="totalPayment()" placeholder="Mã giảm giá nếu có">

                            <h4 class="mb-1">Tổng thanh toán</h4>
                            <h3 class="mb-1 fw-bold text-danger"><span id="total"><?= format_cash($price['monthly']['amount'] - $price['monthly']['amount'] * $ck / 100) ?></span>₫</h3>

                            <button  type="button" class="btn btn-primary w-100" id="btnOrder" onclick="confirmAction(<?=$row['id']?>)">Thanh Toán</button>

                            <a href="/cloudvps" class="d-block text-center mt-2 text-primary">Quay lại</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</main>

<script>
    var vpsid = <?=$id?>;
    const items = document.querySelectorAll('.item');
    items.forEach(item => {
        item.addEventListener('click', () => {
            const duration = item.dataset.duration;
            const price = item.dataset.price;
            const cycle = item.dataset.cycle;
            items.forEach(i => {
                i.classList.remove('border-2', 'active-select');
            });
            item.classList.add('border-2', 'active-select');
            updateBillingCycle(duration, price);
            document.getElementById("cycle").innerHTML = cycle;
        });
    });

    const oss = document.querySelectorAll('.os');
    oss.forEach(os => {
        os.addEventListener('click', () => {
            const valueos = os.dataset.osid;
            oss.forEach(i => {
                i.classList.remove('border-2', 'active-select');
            });
            os.classList.add('border-2', 'active-select');
            updateos(valueos);
        });
    });


    let billingcycle = "monthly";
    let osid = "";

    function updateBillingCycle(duration, price) {
        billingcycle = duration;
        document.getElementById("total").innerHTML = format_cash(price);
        totalPayment();
    }

    function updateos(value) {
        osid = value;
    }

    function format_cash(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    let getInputByClass = document.getElementsByClassName("input-touchspin");

    (function() {
        Array.from(getInputByClass).forEach((elem, i) => {
            let inputData = parseInt(elem.getAttribute("value"));

            let isIncrement = elem.parentNode.querySelectorAll(".increment-touchspin");
            let isDecrement = elem.parentNode.querySelectorAll(".decrement-touchspin");

            if (isIncrement.length > 0) {
                isIncrement[0].addEventListener("click", () => {
                    if (inputData < 10) {
                        inputData++;
                        elem.setAttribute("value", inputData);
                        updateElements(elem.getAttribute("id"), inputData);
                    }
                    totalPayment();
                });
            }

            if (isDecrement.length > 0) {
                isDecrement[0].addEventListener("click", () => {
                    if (inputData > 0) {
                        inputData--;
                        elem.setAttribute("value", inputData);
                        updateElements(elem.getAttribute("id"), inputData);
                    }
                    totalPayment();
                });
            }
        });
    })();

    function updateElements(type, value) {
        document.getElementById("total" + type).innerHTML = value + (type === "disk" ? "0" : "") + (type === "cpu" ? " Core" : " GB");
    }

    function totalPayment() {
        $('#total').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...');
        var month = $("#month").val();
        $.ajax({
            url: "/model/total/cash",
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                vpsid: vpsid,
                billingcycle: billingcycle,
                cpu: $("#cpu").val(),
                ram: $("#ram").val(),
                disk: $("#disk").val(),
                coupon: $("#coupon").val(),
                action: 'cloudvps'
            },
            success: function(respone) {
                $("#total").html(respone.total);
            },
            error: function() {
                showMessage('Không thể tính kết quả thanh toán', 'error');
            }
        });
    }
    const confirmAction = (id) => {
        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn đồng ý thực hiện thanh toán vps?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                await Item(id);
            }
        });
    }

    const Item = async (id) => {
        Swal.fire({
            icon: "info",
            title: "Đang xử lý!",
            html: "Không được tắt trang này, vui lòng đợi trong giây lát!",
            timerProgressBar: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
            willClose: () => {},
        });


        $.ajax({
            url: '/model/order/cloudvps',
            method: "POST",
            dataType: "JSON",
            data: {
                csrf_token: csrf_token,
                vpsId: id,
                os: osid,
                billingcycle: billingcycle,
                cpu: $("#cpu").val(),
                ram: $("#ram").val(),
                disk: $("#disk").val(),
                coupon: $("#coupon").val(),
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire('Thành công',
                        `${result.msg}`,
                        'success').then(() => {
                        window.location.href = result.url;
                    });
                } else {
                    Swal.fire('Thất Bại', result.msg, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Thất Bại', xhr.responseText, 'error');
            }
        });
    }
</script>

<?php require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'); ?>