<?php
namespace WEBP\Scripts;

class APWEBP_Converter_Scripts {

    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'additional_scripts_admin'));
    }

    public function additional_scripts_admin() {
        wp_enqueue_script('jquery-ui-tooltip');
        wp_enqueue_script('ap.cookie', plugins_url(APWEBP_DIRECTORY_NAME . '/js/ap.cookie.js'));
        wp_enqueue_script('ap-tabs', plugins_url(APWEBP_DIRECTORY_NAME . '/js/ap-tabs.js'));

        wp_register_script('webp', plugins_url(APWEBP_DIRECTORY_NAME . '/js/webp.js'));
        wp_localize_script('webp', 'apwebp_ajax', array('ajaxurl' => admin_url('admin-ajax.php'), 'pluginimg' => plugins_url(APWEBP_DIRECTORY_NAME . '/images/')));
        wp_enqueue_script('webp');

        wp_enqueue_style('jquery-ui', plugins_url(APWEBP_DIRECTORY_NAME . '/css/jquery-ui.css'));
        wp_enqueue_style('webp-admin', plugins_url(APWEBP_DIRECTORY_NAME . '/css/webp-admin.css'));
    }

}