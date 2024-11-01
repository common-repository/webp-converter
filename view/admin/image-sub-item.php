<li>
  <label class="webp-item">
    <input
      type="checkbox"
      class="webp-images"
      value="<?php echo esc_attr($key . $id_spliter . $query_images->post->ID); ?>"
    />
    <?php echo esc_html($value['file']); ?>
  </label>
  <span
    class="webp-status-image"
    id="status-<?php echo esc_attr($key . $id_spliter . $query_images->post->ID); ?>"
  ><?php echo apwebp_image_status($query_images->post->ID, $key); ?></span>
  <span
    class="webp-status-text"
    id="status-text-<?php echo esc_attr($key . $id_spliter . $query_images->post->ID); ?>"
  ><?php echo apwebp_image_status_text($query_images->post->ID, $key); ?></span>
</li>
