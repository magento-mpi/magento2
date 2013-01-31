/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    /**
     * Product gallery widget
     */
    $.widget('mage.productGallery', {
        options: {
            item: '[data-role="image"]',
            template: '.image-template',
            types: null
        },

        /**
         * Gallery creation
         * @protected
         */
        _create: function() {
            this.options.types = this.options.types || this.element.data('types');
            this.options.images = this.options.images || this.element.data('images');
            this.$template = this.element.find(this.options.template);
            this._bind();
            $.each(this.options.images, $.proxy(function(index, imageData) {
                this.element.trigger('addItem', imageData);
            }, this));
        },

        /**
         * Bind handler to elements
         * @protected
         */
        _bind: function() {
            var events = {
                addItem: '_addItem',
                removeItem: '_removeItem',
                setImageType: '_setImageType',
                setPosition: '_setPosition',
                resort: '_resort',
                'click .remove': function(event) {
                    var $imageContainer = $(event.currentTarget).closest(this.options.item);
                    this.element.trigger('removeItem', $imageContainer.data('imageData'));
                },
                'click .main-control': function(event) {
                    var $imageContainer = $(event.currentTarget).closest(this.options.item);
                    var imageData = $imageContainer.data('imageData');
                    this._setMain(imageData);
                },
                'change [data-role="type-selector"]': '_changeType'
            };
            events['click ' + this.options.item] = function() {
                $(event.currentTarget).toggleClass('active');
            };
            events['dblclick ' + this.options.item] = function(event) {
                this._showDialog($(event.currentTarget).data('imageData'));
            }
            this._on(events);

            this.element.sortable({
                distance: 8,
                tolerance: "pointer",
                cancel: 'input, button, .ui-dialog',
                update: $.proxy(function() {
                    this.element.trigger('resort');
                }, this)
            });
        },

        /**
         * Set image as main
         * @param {Object} imageData
         * @private
         */
        _setMain: function(imageData) {
            var imageForTypes = $.map(this.options.types, function(el) {
                return el.value
            });
            var isAllSame = $.grep(imageForTypes,function(el, index) {
                return index == imageForTypes.indexOf(el);
            }).length == 1;

            if (isAllSame) {
                $.each(this.options.types, $.proxy(function(index, image) {
                    this.element.trigger('setImageType', {
                        type: index,
                        imageData: imageData
                    });
                }, this));
            } else {
                this.element.trigger('setImageType', {
                    type: 'image',
                    imageData: imageData
                });
            }
        },

        /**
         * Set image
         * @param event
         * @private
         */
        _changeType: function(event) {
            var $checkbox = $(event.currentTarget);
            var $imageContainer = $checkbox.closest(this.options.item);
            this.element.trigger('setImageType', {
                type: $checkbox.val(),
                imageData: $checkbox.is(':checked') ? $imageContainer.data('imageData') : null
            });
        },

        /**
         * Find element by fileName
         * @param {Object} data
         * @returns {Element}
         */
        findElement: function(data) {
            return this.element.find(this.options.item).filter(function() {
                return $(this).data('imageData').file == data.file;
            }).first();
        },

        /**
         * Add image
         * @param event
         * @param imageData
         * @private
         */
        _addItem: function(event, imageData) {
            var count = this.element.find(this.options.item).length;
            var imageData = $.extend({
                file_id: Math.random().toString(33).substr(2, 18),
                disabled: 0,
                position: count + 1
            }, imageData);

            var element = this.$template.tmpl(imageData).data('imageData', imageData);
            if (count == 0) {
                element.prependTo(this.element);
            } else {
                element.insertAfter(this.element.find(this.options.item + ':last'));
            }

            this._setMain(imageData);
        },

        /**
         * Remove Image
         * @param {jQuery.Event} event
         * @param imageData
         * @private
         */
        _removeItem: function(event, imageData) {
            var $imageContainer = this.findElement(imageData);
            $imageContainer.hide().find('.is-removed').val(1);;
        },

        /**
         * Set image type
         * @param event
         * @param data
         * @private
         */
        _setImageType: function(event, data){
            this.element.find('.type-' + data.type).hide();
            if (data.imageData) {
                this.options.types[data.type].value = data.imageData.file;
                this.findElement(data.imageData).find('.type-' + data.type).show();
            } else {
                this.options.types[data.type].value = null;
            }
            this.element.find('.image-' + data.type).val(this.options.types[data.type].value || 'no_selection');
        },

        /**
         * Resort images
         * @private
         */
        _resort: function() {
            $(this).find('.position').each(function(index) {
                var value = $(this).val();
                if (value != index + 1) {
                    this.element.trigger('moveElement', {
                        imageData: $(this).closest(this.options.item).data('imageData'),
                        position: index + 1
                    });
                    $(this).val(index + 1);
                }
            });
        },

        /**
         * Set image position
         * @param event
         * @param data
         * @private
         */
        _setPosition: function(event, data) {
            var $element = this.element.findElement(data.imageData);
            var curIndex = this.element.find(this.options.item).index($element);
            var newPosition = data.position + (curIndex > data.position ? -1 : 0);
            if (data.position != curIndex) {
                if (data.position == 0) {
                    this.element.prepend($element);
                } else {
                    $element.insertAfter(
                        this.element.find(this.options.item).eq(newPosition)
                    );
                }
                this.element.trigger('resort');
            }
        }
    });

    // Extension for mage.productGallery - Add advanced settings dialog
    $.widget('mage.productGallery', $.mage.productGallery, {
        options: {
            dialogTemplate: '.dialog-template'
        },

        /**
         * Bind handler to elements
         * @protected
         */
        _bind: function() {
            this._super();
            this._on({
                'click .remove': function(event) {
                    var $imageContainer = $(event.currentTarget).closest(this.options.item);
                    var dialog = $imageContainer.data('dialog');
                    if (dialog) {
                        dialog.dialog('close');
                    }
                }
            });
        },

        /**
         * Show dialog
         * @param imageData
         * @private
         */
        _showDialog: function(imageData) {
            var $imageContainer = this.findElement(imageData);
            var dialogElement = $imageContainer.data('dialog');
            if (!dialogElement) {
                var $template = this.element.find(this.options.dialogTemplate);
                dialogElement = $template.tmpl(imageData).dialog($.extend({
                    id: this.element.attr('id') + '-dialog',
                    minWidth: 560,
                    autoOpen: false,
                    modal: true,
                    resizable: false
                }, $template.data(), this.options.dialog || {}));

                dialogElement.on("dialogopen", $.proxy(function() {
                    dialogElement.closest('.ui-dialog').appendTo($imageContainer);
                    $imageContainer.find('[data-role="type-selector"]').each($.proxy(function(index, checkbox) {
                        var $checkbox = $(checkbox);
                        $checkbox.prop('checked', this.options.types[$checkbox.val()].value == imageData.file)
                    }, this));

                }, this));

                $imageContainer.data('dialog', dialogElement)
            }
            dialogElement.dialog('open');
        }
    });
})(jQuery);
