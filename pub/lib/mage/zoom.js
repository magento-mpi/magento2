/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
    @version 0.1.1
    @requires jQuery
 */

;(function($, document, window, undefined){
    "use strict";
    $.widget('mage.zoom', {
        options: {
            largeImage: null,
            startZoomEvent: 'click',
            stopZoomEvent: 'mouseleave',
            hideDelay: '100',
            effects: {
                show: {effect: "fade", duration: 100},
                hide: {effect: "fade", duration: 100}
            },
            controls:{
                lens: {
                    template: '[data-template=zoom-lens]',
                    opacity: 0.7,
                    background: '#ffffff'
                },
                track: {
                    template: '[data-template=zoom-track]'
                },
                display: {
                    template: '[data-template=zoom-display]',
                    width: 400,
                    height: 400
                },
                notice: {
                    template: '[data-template=notice]',
                    text: null,
                    container: '[data-role=gallery-notice-container]'
                }
            },
            selectors: {
                image: '[data-role=zoom-image]',
                imageContainer: '[data-role=gallery-base-image-container]',
                zoomInner: '[data-role=zoom-inner]',
                track: '[data-role=zoom-track]',
                notice: '[data-role=notice]'
            }
        },
        noticeOriginal: '',

        /**
         * Widget constructor
         * @protected
         */
        _create: function() {
            this._setZoomData();
            this._render();
            this._bind();
            if(this.largeImage[0].complete) {
                this._largeImageLoaded();
            }
            this._hide(this.display);
            this._hide(this.track);
        },

        /**
         * Render zoom controls
         * @protected
         */
        _render: function() {
            this.element.append(this._renderControl('track').append(this._renderControl('lens')));
            this.element.append(this._renderControl('display'))
                .find(this.options.selectors.zoomInner)
                .append(this._renderLargeImage());
            var noticeContainer = this.element.find(this.options.controls.notice.container);
            noticeContainer = noticeContainer.length ?
                noticeContainer :
                this.element;
            noticeContainer.append(this._renderControl('notice'));
        },

        /**
         * Toggle zoom notice
         * @protected
         */
        _toggleNotice: function() {
            this.noticeOriginal = (this.notice.text() !== this.options.controls.notice.text ?
                this.notice.text() :
                this.noticeOriginal);
            if (this.getZoomRatio() > 1 && this.largeImageSrc && !this.activated) {
                this.notice.text(this.options.controls.notice.text);
            } else {
                this.notice.text(this.noticeOriginal);
            }
        },

        /**
         * Render zoom control
         * @param {string} control - name of the control
         * @return {Element} DOM-element
         * @protected
         */
        _renderControl: function(control) {
            var controlData = this.options.controls[control],
                templateData = {},
                css = {};
            switch(control) {
                case 'display':
                    templateData = {img: this.largeImageSrc};
                    css = {
                        width: controlData.width,
                        height: controlData.height
                    };
                    break;
                case 'notice':
                    templateData = {text: controlData.text || ''};
                    break;
            }
            var controlElement = this.element.find(this.options.selectors[control]);
            controlElement = controlElement.length ?
                controlElement :
                $.tmpl($(controlData.template), templateData);
            this[control] = controlElement.css(css);
            return this[control];
        },

        /**
         * Refresh zoom controls
         * @protected
         */
        _refresh: function() {
            this._refreshControl('display');
            this._refreshControl('track');
            this._refreshControl('lens');
        },

        /**
         * Refresh zoom control position and css
         * @param {string} control - name of the control
         * @protected
         */
        _refreshControl: function(control) {
            var controlData = this.options.controls[control],
                position,
                css = {position: 'absolute'};
            switch(control) {
                case 'display':
                    position = {
                        my: 'left+30% top',
                        at: 'left+' + $(this.image).outerWidth() + ' top',
                        of: $(this.image)
                    };
                    break;
                case 'track':
                    $.extend(css, {
                        height: $(this.image).height(),
                        width: $(this.image).width()
                    });
                    position = {
                        my: 'left top',
                        at: 'left top',
                        of: $(this.image)
                    };
                    break;
                case 'lens':
                    $.extend(css, this._calculateLensSize(), {
                        background: controlData.background,
                        opacity: controlData.opacity,
                        left: 0,
                        top: 0
                    });
                    break;
            }
            this[control].css(css);
            if (position) {
                this[control].position(position);
            }
        },

        /**
         * Bind zoom event handlers
         * @protected
         */
        _bind: function() {
            /* Events delegated to this.element, which means that all zoom controls can be changed any time
             *  and not required to re-bind events
             */
            var events = {};
            events[this.options.startZoomEvent + ' ' + this.options.selectors.image] = 'show';
            events[this.options.stopZoomEvent + ' ' + this.options.selectors.track] = function() {
                this._delay(this.hide, this.options.hideDelay || 0);
            };
            events['mousemove ' + this.options.selectors.track] = '_move';
            events.imageupdated = '_onImageUpdated';
            this._on(events);
            this._on(this.largeImage, {
                load: '_largeImageLoaded'
            });
        },

        /**
         * Store initial zoom data
         * @protected
         */
        _setZoomData: function() {
            this.image = this.element.find(this.options.selectors.image);
            this.largeImageSrc = this.options.largeImage ||
                this.element.find(this.image).data('large');
        },

        /**
         * Update zoom when called enable method
         * @override
         */
        enable: function() {
            this._super();
            this._onImageUpdated();
        },

        /**
         * Toggle notice when called disable method
         * @override
         */
        disable: function() {
            this.notice.text(this.noticeOriginal || '');
            this._super();
        },

        /**
         * Show zoom controls
         * @param {Object} e - event object
         */
        show: function(e) {
            e.preventDefault();
            if (this.getZoomRatio() > 1 && this.largeImageSrc) {
                e.stopImmediatePropagation();
                this.activated = true;
                this._show(this.display, this.options.effects.show);
                this._show(this.track, this.options.effects.show);
                this._refresh();
                this.lens.position({
                    my: 'center',
                    at: 'center',
                    of: e,
                    using: $.proxy(this._refreshZoom, this)
                });
                this._toggleNotice();
                this._trigger('show');
            }
        },

        /**
         * Hide zoom controls
         */
        hide: function() {
            this.activated = false;
            this._hide(this.display, this.options.effects.hide);
            this._hide(this.track, this.options.effects.hide);
            this._toggleNotice();
            this._trigger('hide');
        },

        /**
         * Refresh zoom when image is updated
         * @protected
         */
        _onImageUpdated: function() {
            if (!this.image.is($(this.options.selectors.image))) {
                this._setZoomData();
                if (this.largeImageSrc) {
                    this._refreshLargeImage();
                    this._refresh();
                } else {
                    this.hide();
                }
            }
        },

        /**
         * Reset this.ratio when large image is loaded
         * @protected
         */
        _largeImageLoaded: function() {
            this.ratio = null;
            this._toggleNotice();
            $(this.options.selectors.image).trigger('processStop');
        },

        /**
         * Refresh large image (refresh "src" and initial position)
         * @protected
         */
        _refreshLargeImage: function() {
            if (this.largeImage) {
                this.largeImage
                    .prop('src', this.largeImageSrc)
                    .css({top: 0, left: 0});
            }
        },

        /**
         * @return {Element} DOM-element
         * @protected
         */
        _renderLargeImage: function() {
            var image = $(this.options.selectors.image);
            image.trigger('processStart', [image]);
            // No need to create template just for img tag
            this.largeImage = $('<img />', {src: this.largeImageSrc});
            return this.largeImage;
        },

        /**
         * Calculate zoom ratio
         * @return {number}
         * @protected
         */
        getZoomRatio: function() {
            if(this.ratio === null || typeof(this.ratio) === 'undefined') {
                var largeWidth = this.largeImage.width() || this.largeImage.prop('width'),
                    imageWidth = $(this.image).width();
                return largeWidth / imageWidth;
            }
            return this.ratio;
        },

        /**
         * Calculate lens size, depending on zoom ratio
         * @return {Object} object contain width and height fields
         * @protected
         */
        _calculateLensSize: function() {
            var displayData = this.options.controls.display,
                ratio = this.getZoomRatio();
            return {
                width: Math.ceil(displayData.width / ratio),
                height: Math.ceil(displayData.height / ratio)
            };
        },

        /**
         * Refresh position of large image depending of position of zoom lens
         * @param {Object} position
         * @param {Object} ui
         * @protected
         */
        _refreshZoom: function(position, ui) {
            var ratio = this.getZoomRatio();
            $(ui.element.element).css(position);
            this.largeImage.css({top: -(position.top * ratio), left: -(position.left * ratio)});
        },

        /**
         * Mouse move handler
         * @param {Object} e - event object
         * @protected
         */
        _move: function(e) {
            this.lens.position({
                my: "center",
                at: "left top",
                of: e,
                collision: 'fit',
                within: this.image,
                using: $.proxy(this._refreshZoom, this)
            });
        }
    });
})(jQuery, document, window);