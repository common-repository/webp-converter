jQuery(document).ready(function () {
  jQuery.ajax({
    type: "post",
    dataType: "json",
    url: apwebp_ajax.ajaxurl,
    data: { action: "webpPluginStatus" },
    beforeSend: function () {
      jQuery("#key-status-webp").html("<p>Please wait..</p>");
    },
    success: function (res) {
      if (res.status == "success") {
        jQuery("#key-status-webp").html(res.msg);
      } else {
        jQuery("#key-status-webp").html("<p>Error</p>");
      }
    },
  });
});

function webpConverterInit() {
  let images = document.querySelectorAll(".webp-images:checked");
  if (images.length == 0) {
    alert("Please select images to convert!");
    return;
  }
  for (let i = 0, len = images.length; i < len; i++) {
    jQuery("#status-" + images[i].value).html(
      '<img src="' + apwebp_ajax.pluginimg + "/wait.png" + '">'
    );
    jQuery("#status-text-" + images[i].value).html("");
  }
  for (let i = 0, len = images.length; i < len; i++) {
    let iv = images[i].value;
    setTimeout(function () {
      webpDoConvert(iv);
    }, i * 10000);
  }
}

function webpDoConvert(id) {
  jQuery
    .ajax({
      type: "post",
      dataType: "json",
      url: apwebp_ajax.ajaxurl,
      data: { action: "webpDoConvert", id: id },
      beforeSend: function () {
        jQuery("#status-" + id).html(
          '<img src="' + apwebp_ajax.pluginimg + "/loading.gif" + '">'
        );
      },
    })
    .done(function (res) {
      console.log(res);
      if (res.status == "success") {
        jQuery("#status-" + id).html(
          '<img src="' + apwebp_ajax.pluginimg + "/done.png" + '">'
        );
      } else {
        jQuery("#status-" + id).html(
          '<img src="' + apwebp_ajax.pluginimg + "/error.png" + '">'
        );
      }
      jQuery("#status-text-" + id).html(res.msg);
    })
    .fail(function (res) {
      jQuery("#status-" + id).html(
        '<img src="' + apwebp_ajax.pluginimg + "/error.png" + '">'
      );
    });
}

function webpSelectAll() {
  let images = document.querySelectorAll(".webp-images");
  images.forEach((image) => {
    image.checked = true;
  });
}

function webpUnselectAll() {
  let images = document.querySelectorAll(".webp-images");
  images.forEach((image) => {
    image.checked = false;
  });
}

jQuery(function () {
  jQuery(document).tooltip({
    content: function () {
      let src = jQuery(this).attr("title");
      return '<img src="' + src + '" width="80">';
    },
  });
});
