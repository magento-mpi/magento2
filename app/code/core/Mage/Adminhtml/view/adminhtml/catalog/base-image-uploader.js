/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*global media_gallery_contentJsObject*/
// @todo: refactor as widget
function BaseImageUploader(id, maxFileSize) {
    (function ($) {
        var $container = $('#' + id + '-container'),
            $template = $('#' + id + '-template'),
            $dropPlaceholder = $('#' + id + '-upload-placeholder'),
            images = $container.data('images'),
            maxImageCount = 5,
            mainImage = $container.data('main'),
            mainClass = 'main',
            index = 0;

        $container.on('add', function(event, data) {
            if (index < maxImageCount) {
                var $element = $template.tmpl(data);
                $element.insertBefore($dropPlaceholder);
                $element.data('image', data);
                if (data.file == mainImage) {
                    $element.addClass(mainClass)
                }
                index++;
            }
            if (index <= maxImageCount) {
                $dropPlaceholder.hide()
            }
        });

        $.each(images.items, function() {
            $container.trigger('add', this);
        });
        $container.on('click', '.container', function (event) {
            $(this).toggleClass('hover');
        });
        $container.on('click', '.make-main', function (event) {
            var $imageContainer = $(this).closest('.container');
            var image = $imageContainer.data('image');
            $container.find('.container').removeClass(mainClass);
            $imageContainer.addClass(mainClass);
            mainImage = image.file;
            var selector = ".cell-image input[onclick*='" +
                image.file.replace(/([ #;&,.+*~\':"!^$[\]()=>|\/@])/g, '\\$1') + "']";
            $(selector).prop('checked', true).trigger('click');
        });

        $container.on('click', '.close', function (event) {
            var $imageContainer = $(this).closest('.container');
            var image = $imageContainer.data('image');

            var selector = ".cell-remove input[onclick*='" +
                image.file.replace(/([ #;&,.+*~\':"!^$[\]()=>|\/@])/g, '\\$1') + "']";
            $(selector).prop('checked', true).trigger('click');

            $imageContainer.remove();
            index--;
            if (index < maxImageCount) {
                $dropPlaceholder.css('display', 'inline-block');
            }
        });

        $container.sortable({
            axis: "x",
            handle: '.container'
        });

        $dropPlaceholder.on('click', function(e) {
            $('#' + id + '-upload').trigger(e);
        });
        $('#' + id + '-upload').fileupload({
            dataType: 'json',
            dropZone: $dropPlaceholder,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: maxFileSize,
            done: function (e, data) {
                if (!data.result) {
                    return;
                }
                if (!data.result.error) {
                    $container.trigger('add', data.result);

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
