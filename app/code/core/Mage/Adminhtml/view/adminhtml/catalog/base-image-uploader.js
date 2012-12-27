/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*global media_gallery_contentJsObject*/
function BaseImageUploader(id, maxFileSize) {
    (function ($) {
        $('#' + id + '_container').sortable({
            axis: "x",
            handle: '.base-image-uploader'
        });
        $('#' + id + '_upload').fileupload({
            dataType: 'json',
            dropZone: '#' + id + '_container .base-image-uploader',
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: maxFileSize,
            done: function (e, data) {
                if (!data.result) {
                    return;
                }
                if (!data.result.error) {
                    var nextImage = null;
                    $('#' + id + '_container .base-image-uploader').each(function(){
                        var $image = $(this);
                        if ($image.data('image')) {
                            return;
                        }
                        if ($image.data('clicked')) {
                            nextImage = $image;
                            return;
                        }

                        if (nextImage === null){
                            nextImage = $image;
                        }

                    });
                    if (nextImage && data.result.url) {
                        var url = data.result.url;
                        nextImage.attr({
                            src: url,
                            title: url,
                            alt: url
                        })
                        .data('image', url);

                        if (typeof media_gallery_contentJsObject != 'undefined') {
                            media_gallery_contentJsObject.handleUploadComplete(data.result);
                            media_gallery_contentJsObject.imagesValues.image = data.result.file;
                            $.each(['small_image', 'thumbnail'], function () {
                                if (media_gallery_contentJsObject.getFileElement('no_selection',
                                    'cell-' + this + ' input').checked) {
                                    media_gallery_contentJsObject.imagesValues[this] = data.result.file;
                                }
                            });
                            media_gallery_contentJsObject.updateImages();
                        }
                    }


                } else {
                    alert(jQuery.mage.__('File extension not known or unsupported type.'));
                }
            },
            add: function(e, data) {
                $(e.delegateTarget).data('clicked', + new Date());
                $(this).fileupload('process', data).done(function () {
                    data.submit();
                });
            }
        });
    })(jQuery);
}
