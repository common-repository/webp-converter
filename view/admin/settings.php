<div class="wrap">
<?php $this->help_support();?>
<h1 class="wp-heading-inline" style="margin-bottom: 10px;"><?php _e('WEBP Images Settings', 'webp-converter');?></h1>

<?php if (isset($GLOBALS['msg'])) {echo '<div class="updated notice notice-success webp-msg is-dismissible">' . html_entity_decode(esc_html($GLOBALS['msg'])) . '</div>';}?>

<form name="f" method="post" action="">
<input type="hidden" name="option" value="apwebp_save_settings" />
<?php wp_nonce_field('apwebp_options_save_action', 'apwebp_options_save_action_field');?>
<table border="0" class="ap-table" width="100%">
  <tr>
    <td colspan="2">
     <div class="ap-tabs">
        <div class="ap-tab"><?php _e('General', 'webp-converter');?></div>
        <div class="ap-tab"><?php _e('Status', 'webp-converter');?></div>
    </div>

     <div class="ap-tabs-content">
        <div class="ap-tab-content">
        <table width="100%">
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td valign="top" width="300"><strong><?php _e('Enable', 'webp-converter');?></strong></td>
            <td><label><input type="checkbox" name="apwebp_enable" value="yes" <?php echo get_option('apwebp_enable') == 'yes' ? 'checked="checked"' : ''; ?> /> <?php _e('Enable webp images', 'webp-converter');?></label></td>
           </tr>
           <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
           <tr>
          <tr>
            <td valign="top" width="300"><strong><?php _e('Convert only full size images', 'webp-converter');?></strong></td>
            <td><label><input type="checkbox" name="apwebp_dont_conv_image_sizes" value="yes" <?php echo get_option('apwebp_dont_conv_image_sizes') == 'yes' ? 'checked="checked"' : ''; ?> /> <?php _e('Don\'t convert different thumbnail image sizes', 'webp-converter');?></label></td>
           </tr>
           <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td valign="top" width="300"><strong><?php _e('Image quality', 'webp-converter');?></strong></td>
            <td>
              <label><input type="radio" name="apwebp_image_quality" value="50" <?php echo get_option('apwebp_image_quality') == '50' ? 'checked="checked"' : ''; ?> /> 50%</label>

              <label><input type="radio" name="apwebp_image_quality" value="60" <?php echo get_option('apwebp_image_quality') == '60' ? 'checked="checked"' : ''; ?> /> 60%</label>

              <label><input type="radio" name="apwebp_image_quality" value="70" <?php echo get_option('apwebp_image_quality') == '70' ? 'checked="checked"' : ''; ?> /> 70%</label>

              <label><input type="radio" name="apwebp_image_quality" value="80" <?php echo get_option('apwebp_image_quality') == '80' ? 'checked="checked"' : ''; ?> /> 80%</label>

              <label><input type="radio" name="apwebp_image_quality" value="90" <?php echo get_option('apwebp_image_quality') == '90' ? 'checked="checked"' : ''; ?> /> 90%</label>

              <label><input type="radio" name="apwebp_image_quality" value="100" <?php echo get_option('apwebp_image_quality') == '100' ? 'checked="checked"' : ''; ?> /> 100%</label>

          </td>
           </tr>
           <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
           <tr>
          <tr>
            <td valign="top" width="300"><strong><?php _e('Use .htaccess to serve webp images', 'webp-converter');?></strong></td>
            <td><label><input type="checkbox" name="apwebp_use_htaccess" value="yes" <?php echo get_option('apwebp_use_htaccess') == 'yes' ? 'checked="checked"' : ''; ?> /> <?php _e('Enable this so that images can be served in webp format using htaccess. This is useful to serve images which are embedded in the page / post content with full url.', 'webp-converter');?></label></td>
           </tr>
           <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>

          <?php if (get_option('apwebp_use_htaccess') == 'yes' and $hts->get_wpc_ht_data_to_user() !== false) {?>
            <tr>
            <td valign="top"><strong><?php _e('.htaccess on /wp-content/ dir', 'webp-converter');?></strong></td>
            <td><font color="red"><?php _e('Seems like the /wp-content/ dir is not writable. Please create one .htaccess file inside your wp-content dir.', 'webp-converter');?></font></td>
          </tr>
          <tr>
              <td>&nbsp;</td>
              <td><div class="user-hta"><?php echo $hts->get_wpc_ht_data_to_user(); ?></div></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          <?php }?>

          <?php if (get_option('apwebp_use_htaccess') == 'yes' and $hts->get_wpu_ht_data_to_user() !== false) {?>
            <tr>
              <td valign="top"><strong><?php _e('.htaccess on /uploads/ dir', 'webp-converter');?></strong></td>
              <td><font color="red"><?php _e('Seems like the /uploads/ dir is not writable. Please create one .htaccess file inside your uploads dir.', 'webp-converter');?></font></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><div class="user-hta"><?php echo $hts->get_wpu_ht_data_to_user(); ?></div></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          <?php }?>

          <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="submit" value="<?php _e('Save', 'webp-converter');?>" class="button button-primary button-large button-ap-large" /></td>
          </tr>
          </table>
        </div>
        <div class="ap-tab-content">
        <table width="100%">
          <tr>
            <td valign="top" width="300"><strong><?php _e('Plugin API Status', 'webp-converter');?></strong></td>
            <td><p><div id="key-status-webp" class="key-status">...</div><div style="clear:both;"></div></p></td>
          </tr>

          </table>
        </div>
    </div>
  </td>
  </tr>
</table>
</form>
</div>