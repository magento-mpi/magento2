/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
function BaseImageUploader(id, maxFileSize) {
    (function ($) {
        $('#' + id + '_upload').fileupload({
            dataType: 'json',
            dropZone: '#' + id + '_image',
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: maxFileSize,
            done: function (e, data) {
                if (!data.result) {
                    return;
                }
                if (!data.result.error) {
                    $('#' + id + '_image').attr({src: data.result.url,
                        title: data.result.url,
                        alt: data.result.url});
                    $('#' + id).val(data.result.file);
                    if (typeof media_gallery_contentJsObject != 'undefined') {
                        media_gallery_contentJsObject.handleUploadComplete(data.result);
                        media_gallery_contentJsObject.imagesValues.image = data.result.file;
                        media_gallery_contentJsObject.updateImages();
                    }
                } else {
                    alert(jQuery.mage.__('File extension not known or unsupported type.'));
                }
            },
            add: function(e, data) {
                $(this).fileupload('process', data).done(function () {
                    data.submit();
                });
            }
        });
    })(jQuery);
}
