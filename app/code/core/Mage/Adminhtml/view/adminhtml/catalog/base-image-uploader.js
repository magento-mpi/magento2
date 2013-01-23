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
            $galeryContainer = $('#media_gallery_content-container'),
            mainClass = 'base-image',
            currentImageCount = 0,
            maximumImageCount = 5;

        var findElement = function (data) {
            return $container.find('.container').filter(function () {
                return $(this).data('image').file == data.file;
            }).first();
        }

        $galeryContainer.on('setImageType', function (event, data) {
            if (data.type == 'image') {
                $container.find('.' + mainClass).removeClass(mainClass);
                findElement(data.imageData).addClass(mainClass);
            }
        });

        $galeryContainer.on('add', function(event, data) {
            var $element = $template.tmpl(data);
                $element.insertBefore($dropPlaceholder).data('image', data);
            currentImageCount++;
            if (currentImageCount > maximumImageCount) {
                $element.hide();
            }
            if (currentImageCount >= maximumImageCount) {
                $dropPlaceholder.hide();
            }
        });

        $galeryContainer.on('removeImage', function (event, image) {
            findElement(image).remove();
            currentImageCount--;
            if (currentImageCount < maximumImageCount) {
                $dropPlaceholder.show();
            }
        });

        $galeryContainer.on('moveElement', function (event, data) {
            var $element = findElement(data.imageData);
            var index = $container.find('.container').index($element);
            if (data.position - 1 == 0) {
                $container.prepend($element);
            } else {
                var $after = $container.find('.container').eq(data.position - 1);
                if (!$element.is($after)) {
                    $element.insertAfter($after);
                }
            }
            $container.find('.container').each(function (index) {
                $(this)[index < maximumImageCount ? 'show' : 'hide']();
            });
        });


        $container.on('click', '.container', function (event) {
            $(this).toggleClass('active').siblings().removeClass('active');
        });

        $container.on('click', '.make-main', function (event) {
            var data = $(this).closest('.container').data('image');
            $galeryContainer.find('.image-container').filter(function () {
                return $(this).data('imageData').file == data.file;
            }).first().find('.main-control').trigger('click');
        });

        $container.on('click', '.close', function (event) {
            $galeryContainer.trigger('removeImage', $(this).closest('.container').data('image'));
        });

        $container.sortable({
            axis: 'x',
            items: '.container',
            distance: 8,
            tolerance: "pointer",
            stop: function (event, data) {
                $galeryContainer.trigger('setPosition', {
                    imageData: data.item.data('image'),
                    position: $container.find('.container').index(data.item)
                });
                $galeryContainer.trigger('resort');
            }
        }).disableSelection();

        $dropPlaceholder.on('click', function(e) {
            $('#' + id + '-upload').trigger(e);
        });

        $('#' + id + '-upload').fileupload({
            dataType: 'json',
            dropZone: $dropPlaceholder,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: maxFileSize,
            done: function (event, data) {
                $dropPlaceholder.text('');
                $dropPlaceholder.removeClass('in-progress');
                if (!data.result) {
                    return;
                }
                if (!data.result.error) {
                    $galeryContainer.trigger('add', data.result);
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
                $dropPlaceholder.text(progress + '%')
                $dropPlaceholder.addClass('in-progress');
            }
        });

        if ($('label[for=image]').text() == 'Base Image') {
            $('label[for=image]').text('Images');
        }
    })(jQuery);
}
