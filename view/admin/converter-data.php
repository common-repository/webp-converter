<div class="wrap">
  <?php $this->pro_add();?>
  <?php $this->help_support();?>

  <div class="webp-converter">

    <div class="webp-top">
        <a href="javascript:webpSelectAll()" class="button button-blue button-ap-large"><?php _e('Select All', 'webp-converter');?></a>
        <a href="javascript:webpUnselectAll()" class="button button-red button-ap-large"><?php _e('Unselect All', 'webp-converter');?></a>
        <a href="javascript:webpConverterInit()" class="button button-green button-ap-large"><?php _e('Start WEBP Image Converter', 'webp-converter');?></a>
    </div>
<?php
$paged = isset($_GET['paged']) ? (int) sanitize_text_field($_GET['paged']) : 1;
$query_images_args = array(
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'post_status' => 'inherit',
    'posts_per_page' => $per_page,
    'paged' => $paged,
);
$query_images = new WP_Query($query_images_args);
?>
    <ul class="webp-main-images">
    <?php
if ($query_images->have_posts()) {
    while ($query_images->have_posts()) {
        $query_images->the_post();
        $image_datas = wp_get_attachment_metadata($query_images->post->ID);
        $thumbnail = wp_get_attachment_image_src($query_images->post->ID, 'thumbnail');
        $thumbnail_src = $thumbnail[0];
        $info = pathinfo($image_datas['file']);
        include APWEBP_DIRECTORY_PATH . '/view/admin/image-item.php';
    }
}
?>
    </ul>

  </div>


  <div class="webp-converter">

  <div class="tablenav bottom">

<div class="tablenav-pages"><span class="displaying-num"><?php echo esc_html($query_images->found_posts); ?> <?php _e('items', 'webp-converter');?></span>
<span class="pagination-links">
<?php
$big = 999999999;
echo paginate_links(array(
    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
    'format' => '?paged=%#%',
    'current' => max(1, $paged),
    'total' => $query_images->max_num_pages,
    'before_page_number' => '<span class="tablenav-pages-navspan button">',
    'after_page_number' => '</span>',
    'prev_text' => __('<span class="tablenav-pages-navspan button">‹</span>'),
    'next_text' => __('<span class="tablenav-pages-navspan button">›</span>'),
));
?>
</span>
</div>
<br class="clear">
</div>
  </div>

  <?php wp_reset_postdata();?>
</div>