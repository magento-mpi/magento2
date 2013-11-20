/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.integration', {
        options: {
            authType: null, // Auth type : Manual or Oauth
            formSelector: null,
            endpointIdSelector: null,
            endpointContainerClassSelector: null,
            requiredClass: 'required-entry'
        },

        /**
         * Initialize integration widget
         * @private
         */
        _init: function() {
            this._toggleEndpoint();
        },

        /**
         * Bind a click handler to the widget's context element.
         * @private
         */
        _create: function() {
            this._on({
                change: '_toggleEndpoint'
            });
            this._on($(this.options.formSelector), {
                submit: '_resetEndpoint'
            });
        },

        /**
         * Toggle the visibility of the endpoint field based on Auth thype selected
         * @private
         */
        _toggleEndpoint: function() {
            var isOauth =  parseInt(this.element.val()) === this.options.authType;
            $(this.options.endpointContainerClassSelector).children().toggle(isOauth);
            $(this.options.endpointIdSelector).toggleClass(this.options.requiredClass, isOauth);
        },

        /**
         * Reset endpoint field if the Authentication type is not Oauth
         *
         * @private
         */
        _resetEndpoint: function() {
            if (parseInt(this.element.val()) !== this.options.authType) {
                $(this.options.endpointIdSelector).val('');
            }
        }
    });

    window.Integration = function (permissionsDialogUrl, tokensDialogUrl, deactivateDialogUrl, reauthorizeDialogUrl) {
        var url = {
            permissions: permissionsDialogUrl,
            tokens: tokensDialogUrl,
            deactivate: deactivateDialogUrl,
            reauthorize: reauthorizeDialogUrl
        };

        var _showPopup = function (dialog, title, okButton, url) {
            var that = this;

            jQuery.ajax({
                url: url,
                cache: false,
                dataType: 'html',
                data: {form_key: window.FORM_KEY},
                method: 'GET',
                beforeSend: function () {
                    // Show the spinner
                    $('body').trigger('processStart');
                },
                success: function (html) {
                    var popup = $('.integration-popup-container');

                    popup.html(html);

                    var buttons = [{
                        text: $.mage.__('Cancel'),
                        click: function() {
                            $(this).dialog('destroy');
                        }
                    }];

                    // Add confirmation button to the list of dialog buttons
                    buttons.push(okButton);

                    popup.dialog({
                        title: title,
                        modal: true,
                        autoOpen: true,
                        minHeight: 450,
                        minWidth: 600,
                        dialogClass: 'integration-dialog',
                        buttons: buttons
                    });
                },
                complete: function () {
                    // Hide the spinner
                    $('body').trigger('processStop');
                }
            })
        };

        return {
            popup: {
                show: function (ctx) {
                    var dialog = $(ctx).attr('data-row-dialog');

                    if (['permissions', 'deactivate', 'reauthorize', 'tokens'].indexOf(dialog) === -1) {
                        throw 'Invalid dialog type';
                    }

                    var integrationId = $(ctx).attr('data-row-id');

                    if (!integrationId) {
                        throw 'Unable to find integration ID';
                    }

                    // Replace placeholder in URL with actual ID
                    var ajaxUrl = url[dialog].replace(':id', integrationId);

                    try {
                        // Get integration name either from current element or from neighbor column
                        var integrationName = $(ctx).attr('data-row-name')
                            || $(ctx).parents('tr').find('.col-name').html().trim();
                    } catch (e) {
                        throw 'Unable to find integration name';
                    }

                    var okButton = {
                        permissions: {
                            text: $.mage.__('Allow'),
                            'class': 'primary',
                            // This data is going to be used in the next dialog
                            'data-row-id': integrationId,
                            'data-row-name': integrationName,
                            'data-row-dialog': 'tokens',
                            click: function () {
                                // Find the 'Allow' button and clone - it has all necessary data, but is going to be
                                // destroyed along with the current dialog
                                var ctx = $(this).parent().find('button.primary').clone(true);
                                $(this).dialog('destroy');
                                // Make popup out of data we saved from 'Allow' button
                                integration.popup.show(ctx);
                            }
                        },
                        tokens: {
                            text: $.mage.__('Activate'),
                            'class': 'primary',
                            click: function () {
                                alert('Not implemented');
                            }
                        }
                    };

                    _showPopup(dialog, integrationName, okButton[dialog], ajaxUrl);
                }
            }
        };
    };
})(jQuery);
