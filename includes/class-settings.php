<?php
namespace WEBP\Settings;
use WEBP\Htserve as Htserve;

class APWEBP_Converter_Settings {

    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('wp_ajax_webpDoConvert', array($this, 'apwebp_do_convert'));
        add_action('wp_ajax_webpPluginStatus', array($this, 'apwebp_plugin_status'));
        add_action('admin_init', array($this, 'apwebp_options_save_settings'));
        add_filter('set-screen-option', array($this, 'apwebp_set_screen_option'), 10, 3);
    }

    public function apwebp_options_save_settings() {
        if (isset($_POST['option']) and sanitize_text_field($_POST['option']) == "apwebp_save_settings") {

            if (!isset($_POST['apwebp_options_save_action_field']) || !wp_verify_nonce($_POST['apwebp_options_save_action_field'], 'apwebp_options_save_action')) {
                wp_die('Sorry, your nonce did not verify.');
            }

            if (isset($_REQUEST['apwebp_enable'])) {
                update_option('apwebp_enable', sanitize_text_field($_REQUEST['apwebp_enable']));
            } else {
                delete_option('apwebp_enable');
            }

            if (isset($_REQUEST['apwebp_dont_conv_image_sizes'])) {
                update_option('apwebp_dont_conv_image_sizes', sanitize_text_field($_REQUEST['apwebp_dont_conv_image_sizes']));
            } else {
                delete_option('apwebp_dont_conv_image_sizes');
            }

            if (isset($_REQUEST['apwebp_image_quality'])) {
                update_option('apwebp_image_quality', sanitize_text_field($_REQUEST['apwebp_image_quality']));
            } else {
                delete_option('apwebp_image_quality');
            }

            do_action('apwebp_save_settings');

            $GLOBALS['msg'] = apply_filters('webp_msg_filter', __('Plugin data successfully updated.', 'webp-converter') . '<br>');
        }
    }

    public function admin_menu() {
        global $apwebp_list_page;
        $apwebp_list_page = add_menu_page('WEBP Converter', 'WEBP Converter', 'manage_options', 'apwebp_converter_setup_data', array($this, 'converter_data'), 'dashicons-admin-tools');
        add_action("load-$apwebp_list_page", array($this, "apwebp_listing_screen_options"));
        add_submenu_page('apwebp_converter_setup_data', 'Settings', 'Settings', 'activate_plugins', 'apwebp_converter_settings', array($this, 'settings'));
    }

    public function apwebp_set_screen_option($status, $option, $value) {
        if ('images_per_page' == $option) {
            return $value;
        }
    }

    public function apwebp_listing_screen_options() {
        global $apwebp_list_page;
        $screen = get_current_screen();
        if (!is_object($screen) || $screen->id != $apwebp_list_page) {
            return;
        }
        $args = array(
            'label' => __('Images per page', 'webp-converter'),
            'default' => 5,
            'option' => 'images_per_page',
        );
        add_screen_option('per_page', $args);
    }

    public function pro_add() {
        include APWEBP_DIRECTORY_PATH . '/view/admin/pro-add.php';
    }

    public function help_support() {
        include APWEBP_DIRECTORY_PATH . '/view/admin/help.php';
    }

    public function converter_data() {
        global $id_spliter;
        $user = get_current_user_id();
        $screen = get_current_screen();
        $screen_option = $screen->get_option('per_page', 'option');
        $per_page = get_user_meta($user, $screen_option, true);
        include APWEBP_DIRECTORY_PATH . '/view/admin/converter-data.php';
    }

    public function settings() {
        $hts = new Htserve\APWEBP_Htserve;
        include APWEBP_DIRECTORY_PATH . '/view/admin/settings.php';
    }

    function call_web_service($url, $data = [], $headers = []) {
        $response = wp_remote_post($url, array(
            'body' => json_encode($data),
            'headers' => $headers,
        ));
        if (is_wp_error($response)) {
            return;
        } else {
            return $response['body'];
        }
    }

    public function apwebp_do_convert() {
        global $id_spliter, $apwebp_token, $apwebp_api_base;

        $rid = sanitize_text_field($_POST['id']);
        $thumb_type = '';
        if (strpos($rid, $id_spliter) !== false) {
            $id_exp = explode($id_spliter, $rid);
            $thumb_type = $id_exp[0];
            $id = $id_exp[1];
        } else {
            $id = $rid;
        }

        $upload_dir = wp_upload_dir();
        $image_datas = wp_get_attachment_metadata($id);
        $info = pathinfo($image_datas['file']);
        $file_dir = $info['dirname'];
        $base_dir = $upload_dir['basedir'];

        $url = $apwebp_api_base . 'convert-free.php';

        $headers = array(
            'token' => $apwebp_token,
            'Content-Type' => 'application/json',
        );

        if ($thumb_type == '') {
            $filename_no_ext = $info['filename'];
            $filename_ext = $info['basename'];
            $file_full_url = $upload_dir['baseurl'] . '/' . $file_dir . '/' . $filename_ext;

            $post = [
                'url' => $file_full_url,
                'quality' => get_option('apwebp_image_quality'),
            ];

            $response = $this->call_web_service($url, $post, $headers);
        } else {
            $thumb_file = $image_datas['sizes'][$thumb_type];
            $info_thumb = pathinfo($thumb_file['file']);
            $filename_no_ext = $info_thumb['filename'];
            $filename_ext = $info_thumb['basename'];
            $file_full_url = $upload_dir['baseurl'] . '/' . $file_dir . '/' . $filename_ext;

            $post = [
                'url' => $file_full_url,
                'quality' => get_option('apwebp_image_quality'),
            ];
            $response = $this->call_web_service($url, $post, $headers);
        }

        $data = unserialize($response);

        if ($data['status'] == 'success') {
            if (is_writable($base_dir . '/' . $file_dir)) {
                $fp = fopen($base_dir . '/' . $file_dir . '/' . $filename_no_ext . '.webp', 'w');
                fwrite($fp, $data['image']);
                fclose($fp);
                echo json_encode(array('status' => 'success', 'msg' => '<font color="green">' . $data['msg'] . '</font>'));
            } else {
                echo json_encode(array('status' => 'error', 'msg' => '<font color="red">Image not created</font>'));
            }
        } else {
            echo json_encode(array('status' => 'error', 'msg' => '<font color="red">' . $data['msg'] . '</font>'));
        }

        wp_die();
    }

    public function apwebp_plugin_status() {
        $m = '';
        $ret = curl_response_aviplugins(AP_API_BASE . 'api.php?option=webp_free_status_check&site_url=' . urlencode(site_url('/')));
        if ($ret->status == 'success') {
            $m = $ret->msg;
        } else {
            $m = $ret->msg;
        }
        echo json_encode(array('status' => 'success', 'msg' => $m));
        wp_die();
    }

}