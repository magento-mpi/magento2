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
            mainImage = $container.data('main'),
            mainClass = 'base-image',
            currentImageCount = 0,
            maximumImageCount = 5,
            isInitialized = false;

        $container.on('add', function(event, data) {
            if (currentImageCount < maximumImageCount) {
                var $element = $template.tmpl(data);
                $element.insertBefore($dropPlaceholder);
                $element.data('image', data);
                if (isInitialized && !currentImageCount) {
                    $.each('image,small_image,thumbnail'.split(','), function () {
                        if ($('input[name="product[' + this + ']"][value=no_selection]').is(':checked')) {
                            media_gallery_contentJsObject.imagesValues[this] = data.file;
                            if (this == 'image') {
                                mainImage = data.file;
                            }
                        }
                    });
                }
                if (data.file == mainImage) {
                    $element.addClass(mainClass);
                }
                currentImageCount++;
            }
            if (currentImageCount >= maximumImageCount) {
                $dropPlaceholder.hide();
            }
        });

        $container.on('click', '.container', function (event) {
            $(this).toggleClass('hover');
        });
        $container.on('click', '.make-main', function (event) {
            var $imageContainer = $(this).closest('.container'),
                image = $imageContainer.data('image');

            $container.find('.container').removeClass(mainClass);
            $imageContainer.addClass(mainClass);
            mainImage = image.file;
            _getGalleryRowByImage(image).find('input[name="product[image]"]').trigger('click');
        });

        $container.on('click', '.close', function (event) {
            var $imageContainer = $(this).closest('.container'),
                image = $imageContainer.data('image'),
                $galleryRow = _getGalleryRowByImage(image);

            $galleryRow.find('.cell-remove input[type=checkbox]').prop('checked', true).trigger('click');
            $.each('image,small_image,thumbnail'.split(','), function () {
                if ($galleryRow.find('input[name="product[' + this + ']"]').is(':checked')) {
                    $('input[name="product[' + this + ']"][value=no_selection]').prop('checked', true).trigger('click');
                }
            });
            media_gallery_contentJsObject.updateImages();
            $imageContainer.remove();

            currentImageCount--;
            if (currentImageCount < maximumImageCount) {
                $dropPlaceholder.css('display', 'inline-block');
            }
        });

        function _getGalleryRowByImage(image)
        {
            var escapedFileName = image.file.replace(/([ #;&,.+*~\':"!^$[\]()=>|\/@])/g, '\\$1');
            return $('input[onclick*="\'' + escapedFileName + '\'"]').closest('tr');
        }

        $container.sortable({
            axis: 'x',
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
            done: function (event, data) {
                if (!data.result) {
                    return;
                }
                if (!data.result.error) {
                    $container.trigger('add', data.result);
                    if (typeof media_gallery_contentJsObject != 'undefined') {
                        media_gallery_contentJsObject.handleUploadComplete(data.result);
                        media_gallery_contentJsObject.updateImages();
                    }
                } else {
                    alert(jQuery.mage.__('File extension not known or unsupported type.'));
                }
            },
            add: function(event, data) {
                $(this).fileupload('process', data).done(function () {
                    data.submit();
                });
            }
        });

        $.each(images.items || [], function() {
            $container.trigger('add', this);
        });
        isInitialized = true;
    })(jQuery);
}
