<?= $block('css') ?>
<link rel="stylesheet" href="<?= $asset('plugins/wechat-image/css/wechat-image.css') ?>">
<?= $block->end() ?>

<script type="text/html" id="wx-upload-image-tpl">
  <div class="wx-upload-image js-wx-upload-image">
    <div class="wx-upload-image-header">
      <span><%== title ? title : '图片上传' %></span>
    </div>

    <div class="wx-upload-image-body js-upload">
      <ul class="wx-upload-image-cells js-upload-cells">
        <li class="wx-upload-image-add">
          <% if(hasOption) { %>
          <select class="js-upload-image-option img-select">
            <% for(var i in options) {%>
            <option value="<%= options[i].value %>"><%= options[i].key %></option>
            <% } %>
          </select>
          <% } %>
          <div class="wx-upload-image-select-wrp js-select">
            <div class="wx-upload-image-select"></div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</script>

