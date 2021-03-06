<?= $block->css() ?>
<link rel="stylesheet" href="<?= $asset('comps/lightbox2/dist/css/lightbox.min.css') ?>">
<?= $block->end() ?>

<!-- htmllint preset="none" -->
<!-- htmllint tag-name-match="false" -->
<div class="form-group">
  <label class="col-sm-2 control-label">图片</label>

  <div class="col-sm-10">
    <div class="media user-media">
      <% if(config && typeof config.images != 'undefined') { %>
        <% for (var i in config.images) { %>
        <span class="float-left">
          <a class="image-link" href="<%= config.images[i] %>" data-lightbox="pic-set-<%= id %>">
            <img class="image" style="width: 48px" src="<%= config.images[i] %>">
          </a>
        </span>
        <% } %>
      <% } else { %>
        <% for (var i in images) { %>
        <span class="float-left">
          <a class="image-link" href="<%= images[i] %>" data-lightbox="pic-set-<%= id %>">
            <img class="image" style="width: 48px" src="<%= images[i] %>">
          </a>
        </span>
        <% } %>
        <% if (images.length === 0) { %>
          <p class="form-control-plaintext">-</p>
        <% } %>
      <% } %>

    </div>
  </div>
</div>

<?= $block->js() ?>
<script>
  require(['comps/lightbox2/dist/js/lightbox.min'], function () {});
</script>
<?= $block->end() ?>
