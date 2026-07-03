<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';

if ($db->site('vps_status') == 0) {
    die(JsonMsg('error', 'VPSCLOUD đang bảo trì'));
}

$ck_vncloud = $db->site('ck_vncloud');

$addon = addonVps();

foreach ($addon['products']['vps'][0]['product'] as $key => $data) {
    if ($key === 'limit-os') {
        unset($addon['products']['vps'][0]['product'][$key]);
        continue;
    }

    $priceWithCK = array_map(function($info) use ($ck_vncloud) {
        $adjusted_price = $info['amount'] * (1 + $ck_vncloud / 100);
        return [
            'billing_cycle' => $info['billing_cycle'],
            'amount' => floor($adjusted_price / 1000) * 1000 // Round down to nearest 1000
        ];
    }, $data['pricing']);

    $priceJson = json_encode($priceWithCK);

    $row = $db->get_row("SELECT * FROM `tbl_cloudvps` WHERE `product_id` = '" . $data['product_id'] . "' AND `site` = 'VNCLOUD'");

    if (!$row) {
        $create = $db->insert("tbl_cloudvps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'detail' => json_encode($data),
            'pricing' => json_encode($data['pricing']),
            'price' => $priceJson,
            'created_at' => gettime(),
            'updated_at' => gettime(),
            'site' => 'VNCLOUD'
        ));
        if ($create) {
            echo '[<b style="color:green">-</b>] Thêm gói ' . $data['name'] . ' thành công.' . PHP_EOL;
        }
    } else {
        $db->update("tbl_cloudvps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'detail' => json_encode($data),
            'pricing' => json_encode($data['pricing']),
            'price' => $priceJson,
            'updated_at' => gettime(),
        ), " `id` = '" . $row['id'] . "' ");
        echo '[<b style="color:blue">-</b>] Cập nhật gói ' . $data['name'] . ' thành công.' . PHP_EOL;
    }
}

if ($db->site('vps_statusnest') == 0) {
    die(JsonMsg('error', 'CLOUDNEST đang bảo trì'));
}

$ck_cloudnest = $db->site('ck_cloudnest');

$addon = addonVpsCloudNest();

foreach ($addon['products']['vps'][0]['product'] as $key => $data) {
    if ($key === 'limit-os') {
        unset($addon['products']['vps'][0]['product'][$key]);
        continue;
    }

    $priceWithCKNEST = array_map(function($info) use ($ck_cloudnest) {
        $adjusted_price = $info['amount'] * (1 + $ck_cloudnest / 100);
        return [
            'billing_cycle' => $info['billing_cycle'],
            'amount' => floor($adjusted_price / 1000) * 1000 // Round down to nearest 1000
        ];
    }, $data['pricing']);

    $priceJsonNEST = json_encode($priceWithCKNEST);

    $row = $db->get_row("SELECT * FROM `tbl_cloudvps` WHERE `product_id` = '" . $data['product_id'] . "' AND `site` = 'CLOUDNEST'");
    if (!$row) {
        $create = $db->insert("tbl_cloudvps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'detail' => json_encode($data),
            'pricing' => json_encode($data['pricing']),
            'price' => $priceJsonNEST,
            'created_at' => gettime(),
            'updated_at' => gettime(),
            'site' => 'CLOUDNEST'
        ));
        if ($create) {
            echo '[<b style="color:green">-</b>] Thêm gói ' . $data['name'] . ' thành công.' . PHP_EOL;
        }
    } else {
        $db->update("tbl_cloudvps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'detail' => json_encode($data),
            'pricing' => json_encode($data['pricing']),
            'price' => $priceJsonNEST,
            'updated_at' => gettime(),
        ), " `id` = '" . $row['id'] . "' ");
        echo '[<b style="color:blue">-</b>] Cập nhật gói ' . $data['name'] . ' thành công.' . PHP_EOL;
    }
}

if ($db->site('vps_statush2') == 0) {
    die(JsonMsg('error', 'H2CLOUD đang bảo trì'));
}

$ck_h2cloud = $db->site('ck_h2cloud');

$addon = addonVpsH2();

foreach ($addon['products']['vps'][0]['product'] as $key => $data) {
    if ($key === 'limit-os') {
        unset($addon['products']['vps'][0]['product'][$key]);
        continue;
    }

    $priceWithCKH2 = array_map(function($info) use ($ck_h2cloud) {
        $adjusted_price = $info['amount'] * (1 + $ck_h2cloud / 100);
        return [
            'billing_cycle' => $info['billing_cycle'],
            'amount' => floor($adjusted_price / 1000) * 1000 // Round down to nearest 1000
        ];
    }, $data['pricing']);

    $priceJsonH2 = json_encode($priceWithCKH2);

    $row = $db->get_row("SELECT * FROM `tbl_cloudvps` WHERE `product_id` = '" . $data['product_id'] . "' AND `site` = 'H2CLOUD'");
    if (!$row) {
        $create = $db->insert("tbl_cloudvps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'detail' => json_encode($data),
            'pricing' => json_encode($data['pricing']),
            'price' => $priceJsonH2,
            'created_at' => gettime(),
            'updated_at' => gettime(),
            'site' => 'H2CLOUD'
        ));
        if ($create) {
            echo '[<b style="color:green">-</b>] Thêm gói ' . $data['name'] . ' thành công.' . PHP_EOL;
        }
    } else {
        $db->update("tbl_cloudvps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'detail' => json_encode($data),
            'pricing' => json_encode($data['pricing']),
            'price' => $priceJsonH2,
            'updated_at' => gettime(),
        ), " `id` = '" . $row['id'] . "' ");
        echo '[<b style="color:blue">-</b>] Cập nhật gói ' . $data['name'] . ' thành công.' . PHP_EOL;
    }
} 