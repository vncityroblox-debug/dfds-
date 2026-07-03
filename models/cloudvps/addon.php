<?php
require_once realpath($_SERVER["DOCUMENT_ROOT"]) . '/libs/init.php';



$addon = addonVps();

foreach ($addon['products']['addon_vps'][0]['product'] as $data) {
    $row = $db->get_row(" SELECT * FROM `tbl_addon_vps` WHERE `product_id` = '" . $data['product_id'] . "' ");
    if (!$row) {
        $create = $db->insert("tbl_addon_vps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'type_addon' => $data['type_addon'],
            'detail' => json_encode($data),
            'price' => json_encode($data),
            'created_at' => gettime(),
            'updated_at' => gettime(),
        ));
        if ($create) {
            echo '[<b style="color:green">-</b>] Thêm addon ' . $data['name'] . ' thành công.' . PHP_EOL;
        }

    } else {
        $db->update("tbl_addon_vps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'type_addon' => $data['type_addon'],
            'detail' => json_encode($data),
            'price' => json_encode($data),
            'updated_at' => gettime(),
        ), " `id` = '" . $row['id'] . "' ");
        echo '[<b style="color:blue">-</b>] Cập nhật addon ' . $data['name'] . ' thành công.' . PHP_EOL;
    }

}

$addon = addonVpsCloudNest();

foreach ($addon['products']['addon_vps'][0]['product'] as $data) {
    $row = $db->get_row(" SELECT * FROM `tbl_addon_vps` WHERE `product_id` = '" . $data['product_id'] . "' AND `site` = 'CLOUDNEST'");
    if (!$row) {
        $create = $db->insert("tbl_addon_vps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'type_addon' => $data['type_addon'],
            'detail' => json_encode($data),
            'price' => json_encode($data),
            'created_at' => gettime(),
            'updated_at' => gettime(),
            'site' => 'CLOUDNEST'
        ));
        if ($create) {
            echo '[<b style="color:green">-</b>] Thêm addon ' . $data['name'] . ' thành công.' . PHP_EOL;
        }

    } else {
        $db->update("tbl_addon_vps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'type_addon' => $data['type_addon'],
            'detail' => json_encode($data),
            'price' => json_encode($data),
            'updated_at' => gettime(),
        ), " `id` = '" . $row['id'] . "' ");
        echo '[<b style="color:blue">-</b>] Cập nhật addon ' . $data['name'] . ' thành công.' . PHP_EOL;
    }

}


$addon = addonVpsH2(); 

foreach ($addon['products']['addon_vps'][0]['product'] as $data) {
    $row = $db->get_row(" SELECT * FROM `tbl_addon_vps` WHERE `product_id` = '" . $data['product_id'] . "' AND `site` = 'H2CLOUD'");
    if (!$row) {
        $create = $db->insert("tbl_addon_vps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'type_addon' => $data['type_addon'],
            'detail' => json_encode($data),
            'price' => json_encode($data),
            'created_at' => gettime(),
            'updated_at' => gettime(),
            'site' => 'H2CLOUD'
        ));
        if ($create) {
            echo '[<b style="color:green">-</b>] Thêm addon ' . $data['name'] . ' thành công.' . PHP_EOL;
        }

    } else {
        $db->update("tbl_addon_vps", array(
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'type_addon' => $data['type_addon'],
            'detail' => json_encode($data),
            'price' => json_encode($data),
            'updated_at' => gettime(),
        ), " `id` = '" . $row['id'] . "' ");
        echo '[<b style="color:blue">-</b>] Cập nhật addon ' . $data['name'] . ' thành công.' . PHP_EOL;
    }

}
