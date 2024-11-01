<?php
namespace WEBP\Htserve;

class APWEBP_Htserve {

    public $wp_content_dir = '';
    public $wp_content_ht = '';
    public $uploads_dir = '';
    public $uploads_ht = '';
    public $ht_pre = '## START WEBP CONVERTER ##';
    public $ht_post = '## END WEBP CONVERTER ##';

    public function __construct() {
        $this->wp_content_dir = WP_CONTENT_DIR;
        $this->wp_content_ht = $this->wp_content_dir . '/' . '.htaccess';
        $upload_dir = wp_upload_dir();
        $this->uploads_dir = $upload_dir['basedir'];
        $this->uploads_ht = $this->uploads_dir . '/' . '.htaccess';
        add_action('apwebp_save_settings', array($this, 'use_htaccess'));
    }

    public function use_htaccess() {

        $wpc_ht_created = false;
        $wpu_ht_created = false;

        if (isset($_REQUEST['apwebp_use_htaccess'])) {
            $wpc_ht_created = $this->add_wpc_ht_data();
            $wpu_ht_created = $this->add_wpu_ht_data();
            update_option('apwebp_use_htaccess', sanitize_text_field($_REQUEST['apwebp_use_htaccess']));
        } else {
            $this->remove_ht_data();
            delete_option('apwebp_use_htaccess');
            add_filter('webp_msg_filter', array($this, 'files_removed'), 10, 1);
        }

        if ($wpc_ht_created and $wpu_ht_created) {
            add_filter('webp_msg_filter', array($this, 'files_created'), 10, 1);
        }

    }

    function add_wpc_ht_data() {
        if (is_writable($this->wp_content_dir)) {
            if ($this->is_wpc_ht_exists()) {
                $oldht_data = file_get_contents($this->wp_content_ht);
                if (preg_match('/(?<=' . $this->ht_pre . ').*?(?=' . $this->ht_post . ')/si', $oldht_data)) {
                    $ht_data = preg_replace('/(?<=' . $this->ht_pre . ').*?(?=' . $this->ht_post . ')/si', $this->get_wpc_ht_data(), $oldht_data);
                    file_put_contents($this->wp_content_ht, $ht_data);
                } else {
                    $ht_data = PHP_EOL . PHP_EOL . $this->ht_pre . stripslashes($this->get_wpc_ht_data()) . $this->ht_post;
                    file_put_contents($this->wp_content_ht, $ht_data, FILE_APPEND);
                }
            } else {
                $ht_data = $this->ht_pre . stripslashes($this->get_wpc_ht_data()) . $this->ht_post;
                file_put_contents($this->wp_content_ht, $ht_data);
            }
            return true;
        } else {
            add_filter('webp_msg_filter', array($this, 'file_not_writable'), 10, 1);
        }
        return false;
    }

    function add_wpu_ht_data() {
        if (is_writable($this->uploads_dir)) {
            if ($this->is_wpu_ht_exists()) {
                $oldht_data = file_get_contents($this->uploads_ht);
                if (preg_match('/(?<=' . $this->ht_pre . ').*?(?=' . $this->ht_post . ')/si', $oldht_data)) {
                    $ht_data = preg_replace('/(?<=' . $this->ht_pre . ').*?(?=' . $this->ht_post . ')/si', $this->get_wpu_ht_data(), $oldht_data);
                    file_put_contents($this->uploads_ht, $ht_data);
                } else {
                    $ht_data = PHP_EOL . PHP_EOL . $this->ht_pre . stripslashes($this->get_wpu_ht_data()) . $this->ht_post;
                    file_put_contents($this->uploads_ht, $ht_data, FILE_APPEND);
                }
            } else {
                $ht_data = $this->ht_pre . stripslashes($this->get_wpu_ht_data()) . $this->ht_post;
                file_put_contents($this->uploads_ht, $ht_data);
            }
            return true;
        } else {
            add_filter('webp_msg_filter', array($this, 'file_not_writable_uploads'), 10, 1);
        }
        return false;
    }

    public function remove_ht_data() {
        if ($this->is_wpc_ht_exists()) {
            $oldht_data = file_get_contents($this->wp_content_ht);
            if (preg_match('/(?<=' . $this->ht_pre . ').*?(?=' . $this->ht_post . ')/si', $oldht_data)) {
                $ht_data = preg_replace('/(?<=' . $this->ht_pre . ').*?(?=' . $this->ht_post . ')/si', '', $oldht_data);
                file_put_contents($this->wp_content_ht, $ht_data);
            }
        }
        if ($this->is_wpu_ht_exists()) {
            $oldht_data = file_get_contents($this->uploads_ht);
            if (preg_match('/(?<=' . $this->ht_pre . ').*?(?=' . $this->ht_post . ')/si', $oldht_data)) {
                $ht_data = preg_replace('/(?<=' . $this->ht_pre . ').*?(?=' . $this->ht_post . ')/si', '', $oldht_data);
                file_put_contents($this->uploads_ht, $ht_data);
            }
        }
    }

    public function get_wpc_ht_data() {
        $data = PHP_EOL;
        $data .= '<IfModule mod_rewrite.c>' . PHP_EOL;
        $data .= 'RewriteEngine On' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_ACCEPT} image/webp' . PHP_EOL;
        $data .= 'RewriteCond %{DOCUMENT_ROOT}/wp-content/\$1.webp -f' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_HOST}@@%{HTTP_REFERER} ^([^@]*)@@https?://\\\1/.*' . PHP_EOL;
        $data .= 'RewriteRule (.+)\\\.jpg$ /wp-content/\$1.webp [NC,T=image/webp,L]' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_ACCEPT} image/webp' . PHP_EOL;
        $data .= 'RewriteCond %{DOCUMENT_ROOT}/wp-content/\$1.webp -f' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_HOST}@@%{HTTP_REFERER} ^([^@]*)@@https?://\\\1/.*' . PHP_EOL;
        $data .= 'RewriteRule (.+)\\\.jpeg$ /wp-content/\$1.webp [NC,T=image/webp,L]' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_ACCEPT} image/webp' . PHP_EOL;
        $data .= 'RewriteCond %{DOCUMENT_ROOT}/wp-content/\$1.webp -f' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_HOST}@@%{HTTP_REFERER} ^([^@]*)@@https?://\\\1/.*' . PHP_EOL;
        $data .= 'RewriteRule (.+)\\\.png$ /wp-content/\$1.webp [NC,T=image/webp,L]' . PHP_EOL;
        $data .= '</IfModule>' . PHP_EOL;
        $data .= '<IfModule mod_mime.c>' . PHP_EOL;
        $data .= 'AddType image/webp .webp' . PHP_EOL;
        $data .= '</IfModule>' . PHP_EOL;
        $data .= '<IfModule mod_expires.c>' . PHP_EOL;
        $data .= 'ExpiresActive On' . PHP_EOL;
        $data .= 'ExpiresByType image/webp "access plus 1 year"' . PHP_EOL;
        $data .= '</IfModule>' . PHP_EOL;
        return $data;
    }

    public function get_wpu_ht_data() {
        $data = PHP_EOL;
        $data .= '<IfModule mod_rewrite.c>' . PHP_EOL;
        $data .= 'RewriteEngine On' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_ACCEPT} image/webp' . PHP_EOL;
        $data .= 'RewriteCond %{DOCUMENT_ROOT}/wp-content/uploads/\$1.webp -f' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_HOST}@@%{HTTP_REFERER} ^([^@]*)@@https?://\\\1/.*' . PHP_EOL;
        $data .= 'RewriteRule (.+)\\\.jpg$ /wp-content/uploads/\$1.webp [NC,T=image/webp,L]' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_ACCEPT} image/webp' . PHP_EOL;
        $data .= 'RewriteCond %{DOCUMENT_ROOT}/wp-content/uploads/\$1.webp -f' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_HOST}@@%{HTTP_REFERER} ^([^@]*)@@https?://\\\1/.*' . PHP_EOL;
        $data .= 'RewriteRule (.+)\\\.jpeg$ /wp-content/uploads/\$1.webp [NC,T=image/webp,L]' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_ACCEPT} image/webp' . PHP_EOL;
        $data .= 'RewriteCond %{DOCUMENT_ROOT}/wp-content/uploads/\$1.webp -f' . PHP_EOL;
        $data .= 'RewriteCond %{HTTP_HOST}@@%{HTTP_REFERER} ^([^@]*)@@https?://\\\1/.*' . PHP_EOL;
        $data .= 'RewriteRule (.+)\\\.png$ /wp-content/uploads/\$1.webp [NC,T=image/webp,L]' . PHP_EOL;
        $data .= '</IfModule>' . PHP_EOL;
        $data .= '<IfModule mod_mime.c>' . PHP_EOL;
        $data .= 'AddType image/webp .webp' . PHP_EOL;
        $data .= '</IfModule>' . PHP_EOL;
        $data .= '<IfModule mod_expires.c>' . PHP_EOL;
        $data .= 'ExpiresActive On' . PHP_EOL;
        $data .= 'ExpiresByType image/webp "access plus 1 year"' . PHP_EOL;
        $data .= '</IfModule>' . PHP_EOL;
        return $data;
    }

    public function is_wpc_ht_exists() {
        if (is_file($this->wp_content_ht)) {
            return true;
        } else {
            return false;
        }
    }

    public function is_wpu_ht_exists() {
        if (is_file($this->uploads_ht)) {
            return true;
        } else {
            return false;
        }
    }

    public function get_wpc_ht_data_to_user() {
        if (!$this->is_wpc_ht_exists() or !is_writable($this->wp_content_dir)) {
            return nl2br(stripslashes(esc_html($this->ht_pre . $this->get_wpc_ht_data() . $this->ht_post)));
        } else {
            return false;
        }
    }

    public function get_wpu_ht_data_to_user() {
        if (!$this->is_wpu_ht_exists() or !is_writable($this->uploads_dir)) {
            return nl2br(stripslashes(esc_html($this->ht_pre . $this->get_wpu_ht_data() . $this->ht_post)));
        } else {
            return false;
        }
    }

    public function file_not_writable($msg) {
        return $msg . '<font color="red">' . __('/wp-content/ dir not writable. Please check if the dir has correct permissions.', 'webp-converter') . '</font>' . '<br>';
    }

    public function file_not_writable_uploads($msg) {
        return $msg . '<font color="red">' . __('/uploads/ dir not writable. Please check if the dir has correct permissions.', 'webp-converter') . '</font>' . '<br>';
    }

    public function files_created($msg) {
        return $msg . '<font color="green">' . __('.htaccess files are created successfully.', 'webp-converter') . '</font>' . '<br>';
    }

    public function files_removed($msg) {
        return $msg . '<font color="red">' . __('Images will not be served as webp by .htaccess', 'webp-converter') . '</font>' . '<br>';
    }
}