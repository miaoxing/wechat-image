define(function () {

  var Images = function () {

  };

  $.extend(Images.prototype, {
    /**
     * 容器
     */
    $container: null,

    /**
     * 微信接口
     */
    wx: null,

    /**
     * 上传的url
     */
    uploadUrl: '',

    /**
     * 已有的图片
     */
    images: [],

    /**
     * 已有的图片类型选项
     */
    imageOptions: [],

    /**
     * 最大上传数量
     */
    max: 10,

    init: function (options) {
      $.extend(this, options);

      var self = this;
      self.wx.load(function () {
        self.$container.on('click', '.js-select', function () {
          self.wx.chooseImage({
            success: function (res) {
              self.syncUpload(self, res.localIds);
            }
          });
        });

        self.$container.on('click', '.js-image-cell', function () {
          var urls = $(this).closest('.js-upload-cells').find('input').map(function () {
            return this.value;
          }).get();
          self.wx.previewImage({
            current: $(this).find('input').val(),
            urls: urls
          });
        });

        self.$container.on('click', '.js-delete', function (e) {
          var item = $(this).closest('.js-image-cell');
          $.confirm('确定删除该图片吗？', function (result) {
            if (result) {
              item.remove();
            }
          });
          e.stopPropagation();
        });
      });

      // 渲染已有的图片
      $.each(self.images, function (i, image) {
        self.htmlAppend(self, image, i);
      });
    },

    /**
     * 根据配置选择组件
     */
    htmlAppend: function (self, url, i) {
      var optionText = '';
      if ($('.js-upload-image-option').length > 0) {
        var text;
        if (self.imageOptions.length > 0) {
          text = self.imageOptions[i];
        } else {
          text = $('.js-upload-image-option').val();
        }

        optionText = '<div class="img-option-text">' + text + '</div>';
        optionText += '<input type="hidden" name="imageOptions[]" value="' + text + '">';
      }

      var html = '<li class="wx-upload-image-cell js-image-cell" style="background-image:url(' + url + ')"> ' +
        '<input type="hidden" name="images[]" value="' + url + '">' +
        '<i class="delete-image js-delete"><span class="img-close"></span></i>' + optionText + '</li>';

      self.$container.find('.js-upload-cells').prepend(html);
    },

    /**
     * 同步上传
     */
    syncUpload: function (self, localIds) {
      // 上传之前检查是否超出数量
      if (self.$container.find('.js-image-cell').length >= self.max) {
        $.alert('上传的图片不能超过' + self.max + '张');
        return;
      }

      var localId = localIds.shift();
      self.wx.uploadImage({
        localId: localId,
        isShowProgressTips: 1,
        success: function (res) {
          self.uploadServerId(self, res.serverId, localIds);
        },
        error: function () {
          $.alert('上传失败！');
        }
      });
    },

    /**
     * 上传图片到腾讯服务器并
     */
    uploadServerId: function (self, serverId, localIds) {
      $.ajax({
        url: self.uploadUrl,
        type: 'post',
        dataType: 'json',
        data: {
          serverId: serverId
        },
        success: function (ret) {
          if (ret.code == 1) {
            self.htmlAppend(self, ret.url);

            if (localIds.length > 0) {
              self.syncUpload(self, localIds);
            } else {
              $.msg(ret);
            }

          } else {
            $.msg(ret);
          }
        },
        error: function () {
          $.alert('上传失败，请重试');
        }
      });
    }
  });

  return new Images();
});
