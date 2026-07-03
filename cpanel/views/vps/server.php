<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/header.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/sidebar.php');

// Xử lý thêm VPS mới
if (isset($_POST["submit"]) && $data_user['level'] == "admin") {
    $data = [
        'package_name' => Anti_xss($_POST['package_name']),
        'cpu' => Anti_xss($_POST['cpu']),
        'ram' => Anti_xss($_POST['ram']),
        'disk' => Anti_xss($_POST['disk']),
        'ip' => Anti_xss($_POST['ip']),
        'bandwidth' => Anti_xss($_POST['bandwidth']),
        'os' => Anti_xss($_POST['os']),
        'price' => Anti_xss($_POST['price']),
        'period' => Anti_xss($_POST['period'])
    ];

    if ($db->insert('list_vps', $data)) {
        die('<script type="text/javascript">alert("VPS đã được thêm thành công"); window.history.back();</script>');
    } else {
        die('<script type="text/javascript">alert("Thêm VPS thất bại"); window.history.back();</script>');
    }
}

// Xử lý phân trang
if (isset($_GET["limit"])) {
    $limit = (int) Anti_xss($_GET["limit"]);
} else {
    $limit = 10;
}

if (isset($_GET["page"])) {
    $page = Anti_xss((int) $_GET["page"]);
} else {
    $page = 1;
}

$from = ($page - 1) * $limit;
$where = " id > 0 ";
$shortByDate = "";

if (isset($_GET["shortByDate"])) {
    $shortByDate = Anti_xss($_GET["shortByDate"]);
    $yesterday = date("Y-m-d", strtotime("-1 day"));
    $currentWeek = date("W");
    $currentMonth = date("m");
    $currentYear = date("Y");
    $currentDate = date("Y-m-d");
    if ($shortByDate == 1) {
        $where .= " AND created_at LIKE '%" . $currentDate . "%' ";
    }
    if ($shortByDate == 2) {
        $where .= " AND YEAR(created_at) = " . $currentYear . " AND WEEK(created_at, 1) = " . $currentWeek . " ";
    }
    if ($shortByDate == 3) {
        $where .= " AND MONTH(created_at) = '" . $currentMonth . "' AND YEAR(created_at) = '" . $currentYear . "' ";
    }
}

// Truy vấn dữ liệu từ bảng list_vps
$listDatatable = $db->get_list(" SELECT * FROM list_vps WHERE " . $where . " ORDER BY id DESC LIMIT " . $from . "," . $limit . " ");
$totalDatatable = $db->num_rows(" SELECT * FROM list_vps WHERE " . $where . " ORDER BY id DESC ");
$urlDatatable = pagination(("server?limit=" . $limit . "&shortByDate=" . $shortByDate . "&"), $from, $totalDatatable, $limit);
active_license()
?>
<div class="main-content app-content">
  <div class="container-fluid">
    <div class="d-md-flex align-items-center justify-content-between my-4 page-header-breadcrumb">
      <h1 class="page-name fw-semibold fs-18 mb-0">
        <i class="fa-solid fa-layer-group"></i> Quản lý VPS
      </h1>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <button id="delete-selected" class="btn btn-danger btn-sm">
          <i class="fas fa-trash"></i> Xóa đã chọn
        </button>
      </div>
      <div class="col-md-6 text-end">
        <button id="open-card-hide" class="btn btn-primary btn-sm">
          <i class="fa-solid fa-plus"></i> Thêm mới
        </button>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModalScrollable2">
          <i class="fa-solid fa-plus"></i> Thêm qua API
        </button>
      </div>
    </div>

    <!-- Form Thêm VPS -->
    <div class="row">
      <div class="col-xl-12" id="card-hide" style="display:none;">
        <div class="card custom-card">
          <div class="card-body">
            <form method="POST">
              <div class="mb-3">
                <label for="package_name" class="form-label">Tên gói VPS</label>
                <input type="text" name="package_name" id="package_name" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="cpu" class="form-label">CPU</label>
                <input type="text" name="cpu" id="cpu" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="ram" class="form-label">RAM</label>
                <input type="text" name="ram" id="ram" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="disk" class="form-label">Disk</label>
                <input type="text" name="disk" id="disk" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="ip" class="form-label">IP</label>
                <input type="text" name="ip" id="ip" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="bandwidth" class="form-label">Bandwidth</label>
                <input type="text" name="bandwidth" id="bandwidth" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="os" class="form-label">OS</label>
                <input type="text" name="os" id="os" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="price" class="form-label">Giá</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="period" class="form-label">Chu kỳ</label>
                <input type="text" name="period" id="period" class="form-control" required>
              </div>
              <button type="submit" name="submit" class="btn btn-primary">Thêm VPS</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Danh sách VPS -->
    <div class="row">
      <div class="col-xl-12">
        <div class="card custom-card">
          <div class="card-header">
            <div class="card-title">DANH SÁCH VPS</div>
          </div>
          <div class="card-body">
            <form class="row mb-3" method="GET">
              <!-- filter limit & shortByDate giữ nguyên -->
            </form>

            <div class="table-responsive">
              <table class="table table-striped table-hover table-bordered text-nowrap">
                <thead>
                  <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Tên gói</th>
                    <th>Mã sản phẩm</th>
                    <th>CPU</th>
                    <th>RAM</th>
                    <th>Disk</th>
                    <th>IP</th>
                    <th>Bandwidth</th>
                    <th>OS</th>
                    <th>Giá</th>
                    <th>Chu kỳ</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($listDatatable as $row): ?>
                  <tr>
                    <td><input type="checkbox" class="select-item" value="<?= $row['id'] ?>"></td>
                    <td><?= $row['package_name'] ?></td>
                    <td><?= $row['product_id'] ?></td>
                    <td><?= $row['cpu'] ?></td>
                    <td><?= $row['ram'] ?></td>
                    <td><?= $row['disk'] ?></td>
                    <td><?= $row['ip'] ?></td>
                    <td><?= $row['bandwidth'] ?></td>
                    <td><?= $row['os'] ?></td>
                    <td><strong style="color:red"><?= format_cash($row['price']) ?>đ</strong></td>
                    <td><?= $row['period'] ?></td>
                    <td>
                      <a href="/cpanel/vps/server/edit/<?= $row['id'] ?>"
                         class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Chỉnh sửa">
                        <i class="fa fa-pencil-alt"></i> Edit
                      </a>
                      <button onclick="RemoveRow('<?= $row['id'] ?>')"
                              class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Xóa">
                        <i class="fas fa-trash"></i> Delete
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <div class="row">
              <div class="col-md-5">
                <p class="dataTables_info">
                  Showing <?= $limit ?> of <?= format_cash($totalDatatable) ?> Results
                </p>
              </div>
              <div class="col-md-7 text-end">
                <?= $limit < $totalDatatable ? $urlDatatable : '' ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div></div>
    </div>

<div class="modal fade" id="exampleModalScrollable2" tabindex="-1" aria-labelledby="exampleModalScrollable2"
    data-bs-keyboard="false" aria-hidden="true">
    <!-- Scrollable modal -->
    <div class="modal-dialog modal-dialog-centered modal-lg dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel2"><i class="fa-solid fa-plus"></i>
                    Thêm gói VPS                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    
                    <div class="row mb-4">
                        <label class="col-sm-4 col-form-label" for="example-hf-email">Lãi % (<span
                                class="text-danger">*</span>)</label>
                        <div class="col-sm-8">
                             <input type="text" class="form-control" id="ck" name="ck" required>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light " data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="AddServer" class="btn btn-primary shadow-primary btn-wave"><i
                            class="fa fa-fw fa-plus me-1"></i>
                        Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<script>
    function postSaveVps(providerCk) {
        // Hiện thông báo loading
        Swal.fire({
            title: 'Đang xử lý...',
            text: 'Vui lòng chờ trong giây lát',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "/model/admin/vps",
            type: "POST",
            dataType: "JSON",
            data: {
                action: "AddPackVPS",
                ck: providerCk
            },
            success: function (result) {
                if (result.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Thành công",
                        text: result.msg
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Thất bại",
                        text: result.msg
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Lỗi hệ thống",
                    text: "Vui lòng thử lại sau."
                });
            }
        });
    }

    $(document).ready(function () {
        $("#AddServer").on("click", function (e) {
            e.preventDefault();
            const providerCk = $("#ck").val();
            postSaveVps(providerCk);
        });
    });
</script>

<script>
    function postRemove(id) {
        $.ajax({
            url: "/model/admin/delete",
            type: 'POST',
            dataType: "JSON",
            data: {
                action: 'removeListVps',
                id: id
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: result.msg
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thất bại',
                        text: result.msg
                    });
                }
            }
        });
    }

    function RemoveRow(id) {
        Swal.fire({
            title: 'Xác Nhận!',
            text: "Bạn có đồng ý xóa mục ID " + id + " này không ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (confirm) => {
            if (confirm.isConfirmed) {
                postRemove(id);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var button = document.getElementById('open-card-hide');
        var card = document.getElementById('card-hide');

        button.addEventListener('click', function() {
            if (card.style.display === 'none' || card.style.display === '') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<script>
  // toggle form Thêm mới
  document.getElementById('open-card-hide').addEventListener('click', function(){
    let card = document.getElementById('card-hide');
    card.style.display = (card.style.display === 'block') ? 'none' : 'block';
  });

  // select all / deselect all
  document.getElementById('select-all').addEventListener('change', function(){
    document.querySelectorAll('.select-item').forEach(cb => cb.checked = this.checked);
  });

  // Hàm AJAX xóa cũ
  function postRemove(id) {
    return $.ajax({
      url: '/model/admin/delete',
      type: 'POST',
      dataType: 'JSON',
      data: { action: 'removeListVps', id: id }
    });
  }

  function RemoveRow(id) {
    Swal.fire({
      title: 'Xác nhận',
      text: `Bạn có chắc xóa VPS ID ${id}?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Đồng ý',
      cancelButtonText: 'Hủy'
    }).then(res => {
      if (res.isConfirmed) {
        Swal.fire({
          title: 'Đang xóa...',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });

        postRemove(id).done(result => {
          Swal.fire(
            result.status === 'success' ? 'Thành công' : 'Thất bại',
            result.msg,
            result.status
          ).then(() => result.status === 'success' && location.reload());
        });
      }
    });
  }

  // Xóa hàng loạt với loading
  document.getElementById('delete-selected').addEventListener('click', function(){
    let ids = Array.from(document.querySelectorAll('.select-item:checked'))
                   .map(cb => cb.value);
    if (!ids.length) {
      return Swal.fire('Chú ý', 'Vui lòng chọn ít nhất một mục.', 'warning');
    }
    Swal.fire({
      title: 'Xác nhận',
      text: `Bạn muốn xóa ${ids.length} mục đã chọn?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Đồng ý',
      cancelButtonText: 'Hủy'
    }).then(res => {
      if (!res.isConfirmed) return;

      // Hiện loading khi bắt đầu xóa
      Swal.fire({
        title: 'Đang xử lý xóa...',
        text: 'Vui lòng chờ trong giây lát',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
      });

      let queue = ids.slice();
      function next() {
        if (!queue.length) {
          return Swal.fire('Hoàn tất', 'Đã xóa xong tất cả VPS.', 'success')
                   .then(() => location.reload());
        }
        let id = queue.shift();
        postRemove(id).always(next);
      }
      next();
    });
  });
</script>

<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . '/cpanel/views/footer.php');