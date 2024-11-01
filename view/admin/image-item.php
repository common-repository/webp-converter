<li>

  <label class="webp-item">
    <input type="checkbox" class="webp-images" value="<?php echo esc_attr($query_images->post->ID); ?>">
    <a href="post.php?post=<?php echo esc_attr($query_images->post->ID); ?>&action=edit" title="<?php echo esc_attr($thumbnail_src); ?>"><?php echo esc_attr($info['basename']); ?></a>
  </label>

  <span class="webp-status-image" id="status-<?php echo esc_attr($query_images->post->ID); ?>"><?php echo apwebp_image_status($query_images->post->ID, 'full'); ?></span>
  <span class="webp-status-text" id="status-text-<?php echo esc_attr($query_images->post->ID); ?>"><?php echo apwebp_image_status_text($query_images->post->ID, 'full'); ?></span>

  <?php
if (get_option('apwebp_dont_conv_image_sizes') != 'yes') {
    if (is_array($image_datas['sizes'])) {
        echo '<ul class="webp-sub-images">';
        foreach ($image_datas['sizes'] as $key => $value) {
            include APWEBP_DIRECTORY_PATH . '/view/admin/image-sub-item.php';
        }
        echo '</ul>';
    }
}
?>
</li>