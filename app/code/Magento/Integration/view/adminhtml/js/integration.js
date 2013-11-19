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

    $.widget('mage.integrationStatus', {
        options: {
            status: '', // 'activate', 'deactivate', 'reauthorize'
            url: '', // ex.: http://.../integration/activate/id/1?popup_dialog=permissions
            url2: '',
            name: '' // Integration name
        },

        _create: function ()
        {
            this._on({'click': '_showPermissionsPopup'});
        },

        _showPermissionsPopup: function ()
        {
            if (['activate', 'deactivate', 'reauthorize'].indexOf(this.options.status) === -1) {
                throw 'Invalid integration status requested';
            }

            var that = this;

            jQuery.ajax({
                url: this.options.url,
                showLoader: true,
                dataType: 'html',
                data: {formKey: window.FORM_KEY},
                method: 'GET',
                success: function (html) {
                    $('#integration-popup-container').html(html);
                    $('#integration-popup-container').dialog({
                        title: that.options.name,
                        modal: true,
                        autoOpen: true,
                        minHeight: 450,
                        minWidth: 600,
                        buttons: [
                            {
                                text: $.mage.__('Cancel'),
                                click: function() {
                                    $(this).dialog("close");
                                }
                            },
                            {
                                text: $.mage.__('Allow'),
                                'class': 'primary',
                                click: function() {
                                    $(this).dialog("close");
                                    that._showTokenPopup();
                                }
                            }
                        ]
                    }).bind(this);
                }
            });
        },

        _showTokenPopup: function ()
        {
            var that = this;

            jQuery.ajax({
                url: this.options.url2,
                showLoader: true,
                dataType: 'html',
                data: {formKey: window.FORM_KEY},
                method: 'GET',
                success: function (html) {
                    $('#integration-popup-container').html(html);
                    $('#integration-popup-container').dialog({
                        title: that.options.name,
                        modal: true,
                        autoOpen: true,
                        minHeight: 450,
                        minWidth: 600,
                        buttons: [
                            {
                                text: $.mage.__('Cancel'),
                                click: function() {
                                    $(this).dialog("close");
                                }
                            },
                            {
                                text: $.mage.__('Activate'),
                                'class': 'primary',
                                click: function() {
                                    window.alert('Not implemented');
                                }
                            }
                        ]
                    }).bind(this);
                }
            });
        }

    });
})(jQuery);
