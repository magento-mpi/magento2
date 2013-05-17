/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    'use strict';
    $.widget("storeCreation.storeTour", {
        options: {
            tourPrefix: 'store-tour'
        },

        _create: function() {
            this._bind();
        },

        _bind: function() {
            // Close tour wellcome popup
            this.element.on('click', $.proxy(function(e) {
                if ($(e.target).prop('id') === this.options.tourPrefix + '-wrapper') {
                    this.element.find('#action-close-' + this.options.tourPrefix + '-wellcome-popup').click();
                }
            }, this));

            $('#action-close-' + this.options.tourPrefix + '-wellcome-popup')
                .on('click.hideTourPopup', $.proxy(this._closeWelcomePopup, this));

            // Back to storelauncher
            $('#' + this.options.tourPrefix + '-action-back-to-storelauncher')
                .on('click.backToStoreLauncher', $.proxy(this._backToStoreLauncher, this));

            // Start Tour
            $('#action-start-' + this.options.tourPrefix)
                .on('click.startTour', $.proxy(this._startTour, this));

            // Show store tour content
            $('[class^="' + this.options.tourPrefix + '-tag-"]')
                .on('click.showTourContent', $.proxy(function(event) {
                    var elem = $(event.target);
                    if (!elem.is('li')) {
                        elem = elem.parent();
                    }

                    var tourElement = elem.data('tour-element'),
                        tourData = elem.data('tour-selector'),
                        tourContent = $('.' + this.options.tourPrefix + '-data-' + tourElement),
                        tourPrefix = this.options.tourPrefix;

                    $('#' + this.options.tourPrefix + '-tags')
                        .addClass('hidden-' + this.options.tourPrefix + '-tag-labels');
                    $('[class^="' + this.options.tourPrefix + '-tag-"]').fadeIn();

                    elem.add('[class^="' + this.options.tourPrefix + '-description"]')
                        .fadeOut();

                    $('[class^="' + this.options.tourPrefix + '-data"]')
                        .removeClass('active')
                        .fadeOut(400);

                    if (tourContent.children().length == 2) {
                        $(tourData).clone()
                            .prependTo('.' + this.options.tourPrefix + '-data-' + tourElement)
                            .addClass(this.options.tourPrefix + '-demo-content')
                            .data('tour-original', tourData);
                    }
                    this._storeTourDataPosition(tourContent, tourData);

                    tourContent
                        .addClass('active')
                        .fadeIn(function() {
                            var tourDataOffsetTop = $(this).offset().top - 50;
                            $("html, body").animate({
                                scrollTop: tourDataOffsetTop
                            }, 400);
                            $('.' + tourPrefix + '-description-' + tourElement).fadeIn();
                            $('#' + tourPrefix).show();
                        });
                }, this));

            // Toggle store tour mode
            $('#action-toggle-' + this.options.tourPrefix + '-mode')
                .on('click.toggleStoreTourMode', $.proxy(this._toggleStoreTourMode, this));

            $('.action-close-' + this.options.tourPrefix + '-description')
                .on('click.closeTourDescription', $.proxy(function() {
                    this._closeStoreTourDescriptions();
                    $("html, body").animate({
                        scrollTop: 0
                    }, 400);
                }, this));

            var tourPrefix = this.options.tourPrefix;
            $(window).resize(function() {
                if ($('[class^="' + tourPrefix + '-data"]').hasClass('active')) {
                    delay && clearTimeout(delay);
                    var delay = setTimeout(function() {
                        var tourContent = $('[class^="' + tourPrefix + '-data"]').filter('.active'),
                            tourDataClass = tourContent
                                .find('.' + tourPrefix + '-demo-content')
                                .data('tour-original'),
                            tourData = $(tourDataClass).not('.' + tourPrefix + '-demo-content');
                        storeTourDataPosition(tourContent, tourData);
                    }, 100);
                }
            });
        },

        // Set store tour tags position
        _tourTagsSetPosition: function() {
            var topMenuPosition = $('.header-panel').offset().top,
                storeLauncherPosition = $('.store-launcher').offset().top,
                mainMenuPosition = $('.navigation').offset().top;

            $('.' + this.options.tourPrefix + '-tag-main-menu').css({
                top: mainMenuPosition
            });

            $('.' + this.options.tourPrefix + '-tag-top-menu').css({
                top: topMenuPosition - 12
            });

            $('.' + this.options.tourPrefix + '-tag-store-launcher').css({
                top: storeLauncherPosition
            });

            $('.' + this.options.tourPrefix + '-hint').css({
                top: storeLauncherPosition + 200
            });
        },

        _storeTourDataPosition: function(tourContent, tourData) {
            tourContent.css({
                top: $(tourData).offset().top,
                left: $(tourData).offset().left,
                width: $(tourData).outerWidth(),
                height: $(tourData).height()
            });
        },

        // Close store tour description
        _closeStoreTourDescriptions: function() {
            $('[class^="' + this.options.tourPrefix + '-description"]')
                .fadeOut(400);

            $('[class^="' + this.options.tourPrefix + '-data"]')
                .removeClass('active')
                .fadeOut(400);

            $('[class^="' + this.options.tourPrefix + '-tag-"]').fadeIn();

            var tourPrefix = this.options.tourPrefix;
            $('.' + this.options.tourPrefix + '-tags').delay(300).queue(function(next) {
                $(this).removeClass('hidden-' + tourPrefix + '-tag-labels');
                next();
            });
        },

        _closeWelcomePopup: function(event) {
            $('#action-toggle-' + this.options.tourPrefix + '-mode')
                .removeClass('active')
                .attr('title', 'Start Tour');
        },

        _backToStoreLauncher: function(event) {
            $('#action-close-' + this.options.tourPrefix + '-wellcome-popup')
                .trigger('click.hideTourPopup')
                .trigger('click.closeStoreLauncherPopup');
        },

        _toggleStoreTourMode: function(event) {
            var elem = $(event.target).closest('#action-toggle-' + this.options.tourPrefix + '-mode'),
                storeTourWrapper = $('#' + this.options.tourPrefix + '-wrapper'),
                tourPrefix = this.options.tourPrefix;

            if (!elem.hasClass('active')) {
                elem.addClass('active')
                    .attr('title', 'Exit Tour');
                storeTourWrapper.fadeIn(400, function() {
                    $('#' + tourPrefix + '-welcome-popup').animate({
                        top: '50%'
                    });
                });
            } else {
                elem.removeClass('active')
                    .attr('title', 'Start Tour');

                storeTourWrapper
                    .add('#' + this.options.tourPrefix + '-tags')
                    .add('#' + this.options.tourPrefix + '-hint')
                    .fadeOut();
                this._closeStoreTourDescriptions();
            }
        },

        _startTour: function() {
            $('#' + this.options.tourPrefix + '-welcome-popup').animate({top: '-100%'});
            $('#' + this.options.tourPrefix + '-tags')
                .add('[class^="' + this.options.tourPrefix + '-tag-"]')
                .add('#' + this.options.tourPrefix + '-hint')
                .fadeIn();
            this._tourTagsSetPosition();
        }
    });

    $.widget('storeCreation.welcomScreen', $.storeCreation.storeTour, {
        options: {
            tourPrefix: 'welcome-screen',
            ajaxUrl: ''
        },

        _create: function() {
            this._oldCloseWelcomePopup = this._closeWelcomePopup;
            this._closeWelcomePopup =  function(event) {
                this._hideWelcomeScreen();
                return this._oldCloseWelcomePopup(event);
            }

            this._oldBackToStoreLauncher = this._backToStoreLauncher;
            this._backToStoreLauncher =  function(event) {
                this._setWelcomeScreenShown();
                this._hideWelcomeScreen();
                return this._oldBackToStoreLauncher(event);
            }

            this._oldStartTour = this._startTour;
            this._startTour =  function(event) {
                this._setWelcomeScreenShown();
                return this._oldStartTour(event);
            }

            this._oldToggleStoreTourMode = this._toggleStoreTourMode;
            this._toggleStoreTourMode =  function(event) {
                var elem = $(event.target).closest('#action-toggle-' + this.options.tourPrefix + '-mode');
                if (elem.hasClass('active')) {
                    this._hideWelcomeScreen();
                    $('.' + this.options.tourPrefix + '-demo-content').remove();
                }
                return this._oldToggleStoreTourMode(event);
            }
            this._bind();
            $('#action-toggle-' + this.options.tourPrefix + '-mode').trigger('click.toggleStoreTourMode');
        },

        _hideWelcomeScreen: function() {
            $('#action-close-' + this.options.tourPrefix + '-wellcome-popup').hide();
            $('#action-toggle-' + this.options.tourPrefix + '-mode').hide();
        },

        _setWelcomeScreenShown: function() {
            var ajaxOptions = {
                type: 'get',
                showLoader: false,
                dataType: 'json',
                async: true,
                url: this.options.ajaxUrl,

                success: function(result, status) {
                    if (!result.success) {
                        alert(result.error_message);
                    }
                }
            };
            $.ajax(ajaxOptions);
        }
    });
})(window.jQuery);
