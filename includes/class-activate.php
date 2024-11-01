<?php
namespace WEBP\Activate;
use WEBP\Htserve as Htserve;

class APWEBP_Activate {

    static function webp_install() {
        update_option('apwebp_image_quality', 50);
    }

    static function webp_uninstall() {
        $hts = new Htserve\APWEBP_Htserve;
        $hts->remove_ht_data();
        delete_option('apwebp_use_htaccess');
    }
}
