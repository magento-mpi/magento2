/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
// @todo: refactor as widget
function BaseImageUploader(id, maxFileSize) {
    (function ($) {
        var $container = $('#' + id + '-container'),
            $template = $('#' + id + '-template'),
            $dropPlaceholder = $('#' + id + '-upload-placeholder'),
            $galleryContainer = $('#media_gallery_content'),
            mainClass = 'base-image',
            maximumImageCount = 5;

        var findElement = function (data) {
            return $container.find('.container').filter(function () {
                return $(this).data('image').file == data.file;
            }).first();
        };
        var updateVisibility = function() {
            var elementsList = $container.find('.container:not(.removed-item)');
            elementsList.each(function(index) {
                $(this)[index < maximumImageCount ? 'show' : 'hide']();
            });
            $dropPlaceholder[elementsList.length >= maximumImageCount ? 'hide' : 'show']();
        };

        $galleryContainer.on('setImageType', function (event, data) {
            if (data.type == 'image') {
                $container.find('.' + mainClass).removeClass(mainClass);
                if (data.imageData) {
                    findElement(data.imageData).addClass(mainClass);
                }
            }
        });

        $galleryContainer.on('addItem', function(event, data) {
            var $element = $template.tmpl(data);
                $element.data('image', data).insertBefore($dropPlaceholder);
            updateVisibility();
        });

        $galleryContainer.on('removeItem', function (event, image) {
            findElement(image).addClass('removed-item').hide();
            updateVisibility();
        });

        $galleryContainer.on('moveElement', function (event, data) {
            var $element = findElement(data.imageData);
            if (data.position === 0) {
                $container.prepend($element);
            } else {
                var $after = $container.find('.container').eq(data.position);
                if (!$element.is($after)) {
                    $element.insertAfter($after);
                }
            }
            updateVisibility();
        });


        $container.on('click', '.container', function (event) {
            $(this).toggleClass('active').siblings().removeClass('active');
        });

        $container.on('click', '.make-main', function (event) {
            var data = $(this).closest('.container').data('image');
            $galleryContainer.productGallery('setMain', data);
        });

        $container.on('click', '.close', function (event) {
            $galleryContainer.trigger('removeItem', $(this).closest('.container').data('image'));
        });

        $container.sortable({
            axis: 'x',
            items: '.container',
            distance: 8,
            tolerance: 'pointer',
            stop: function (event, data) {
                $galleryContainer.trigger('setPosition', {
                    imageData: data.item.data('image'),
                    position: $container.find('.container').index(data.item)
                });
                $galleryContainer.trigger('resort');
            }
        }).disableSelection();

        $('#' + id + '-upload').fileupload({
            dataType: 'json',
            dropZone: $dropPlaceholder,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: maxFileSize,
            done: function (event, data) {
                $dropPlaceholder.find('.progress-bar').text('').removeClass('in-progress');
                if (!data.result) {
                    return;
                }
                if (!data.result.error) {
                    $galleryContainer.trigger('addItem', data.result);
                } else {
                    alert($.mage.__('File extension not known or unsupported type.'));
                }
            },
            add: function(event, data) {
                $(this).fileupload('process', data).done(function () {
                    data.submit();
                });
            },
            progress: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $dropPlaceholder.find('.progress-bar').addClass('in-progress').text(progress + '%');
            }
        });

    })(jQuery);
}
