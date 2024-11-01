<?php

if (!function_exists('apwebp_text_domain')) {
    function apwebp_text_domain() {
        load_plugin_textdomain('webp-converter', FALSE, basename(dirname(__FILE__)) . '/languages');
    }
}

function is_apwebp_available() {
    if (get_option('apwebp_enable') != 'yes') {
        return false;
    }
    if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') == false) {
        return false;
    }
    return true;
}

function apwebp_ap_convert_url_for_images($image, $attachment_id, $size, $icon) {
    if (!is_apwebp_available()) {
        return $image;
    }
    $url = $image[0];
    $info = pathinfo($url);
    $image_dir = $info['dirname'];
    $filename = $info['filename'];
    $extension = $info['extension'];

    $upload_dir = wp_upload_dir();
    $base_dir = $upload_dir['basedir'];

    if (strpos($url, 'uploads') !== false) {
        $image_dir_exp = explode('uploads', $url);

        $info_2 = pathinfo($image_dir_exp[1]);
        $image_dir_2 = $info_2['dirname'];

        $apwebp_image_path = $base_dir . $image_dir_2 . '/' . $filename . '.webp';

        // check if webp exists
        if (file_exists($apwebp_image_path)) {
            $image[0] = $image_dir . '/' . $filename . '.webp';
            return $image;
        } else {
            return $image;
        }
    } else {
        return $image;
    }
}

function apwebp_ap_convert_url_for_attachments($url) {
    if (!is_apwebp_available()) {
        return $url;
    }
    $info = pathinfo($url);
    $image_dir = $info['dirname'];
    $filename = $info['filename'];
    $extension = $info['extension'];

    $upload_dir = wp_upload_dir();
    $base_dir = $upload_dir['basedir'];

    if (strpos($url, 'uploads') !== false) {
        $image_dir_exp = explode('uploads', $url);

        $info_2 = pathinfo($image_dir_exp[1]);
        $image_dir_2 = $info_2['dirname'];

        $apwebp_image_path = $base_dir . $image_dir_2 . '/' . $filename . '.webp';

        // check if webp exists
        if (file_exists($apwebp_image_path)) {
            return $image_dir . '/' . $filename . '.webp';
        } else {
            return $url;
        }
    } else {
        return $url;
    }
}

function apwebp_check_if_image_exists($url) {
    $info = pathinfo($url);
    $image_dir = $info['dirname'];
    $filename = $info['filename'];
    $extension = $info['extension'];

    $upload_dir = wp_upload_dir();
    $base_dir = $upload_dir['basedir'];

    if (strpos($url, 'uploads') !== false) {
        $image_dir_exp = explode('uploads', $url);

        $info_2 = pathinfo($image_dir_exp[1]);
        $image_dir_2 = $info_2['dirname'];

        $apwebp_image_path = $base_dir . $image_dir_2 . '/' . $filename . '.webp';

        // check if webp exists
        if (file_exists($apwebp_image_path)) {
            $image[0] = $image_dir . '/' . $filename . '.webp';
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function apwebp_image_status($att_id, $size = 'full') {
    $image = wp_get_attachment_image_src($att_id, $size);
    $status = apwebp_check_if_image_exists($image[0]);
    if ($status) {
        return '<img src="' . plugins_url(APWEBP_DIRECTORY_NAME . '/images/done.png') . '">';
    } else {
        return '<img src="' . plugins_url(APWEBP_DIRECTORY_NAME . '/images/error.png') . '">';
    }
}

function apwebp_image_status_text($att_id, $size = 'full') {
    $image = wp_get_attachment_image_src($att_id, $size);
    $status = apwebp_check_if_image_exists($image[0]);
    if ($status) {
        return '<font color="green">' . __('Image already converted', 'webp-converter') . '</font>';
    } else {
        return '<font color="red">' . __('Image not converted', 'webp-converter') . '</font>';
    }
}

if (!function_exists('curl_response_aviplugins')) {
    function curl_response_aviplugins($url) {
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return;
        } else {
            $json = json_decode($response['body']);
            return $json;
        }
    }
}