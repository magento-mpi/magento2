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

    $.widget('mage.integrationPopup', {
        options: {
            dialog: '', // 'permissions', 'deactivate', 'reauthorize', 'tokens'
            url: '', // ex.: http://.../integration/activate/id/1
            name: '' // Integration name
        },

        _create: function ()
        {
            this._on({'click': '_showPopup'});
        },

        dialogOptions: {
            permissions: {
                okButtonLabel: $.mage.__('Allow'),
                minWidth: 600,
            },
            tokens: {
                okButtonLabel: $.mage.__('Activate'),
                minWidth: 700,
            }
        },

        _showPopup: function ()
        {
            if (['permissions', 'deactivate', 'reauthorize', 'tokens'].indexOf(this.options.dialog) === -1) {
                throw 'Invalid dialog type';
            }

            var that = this;
            var dialogOptions = this.dialogOptions[this.options.dialog];

            jQuery.ajax({
                url: this.options.url + '?popup_dialog=' + this.options.dialog,
                showLoader: true,
                dataType: 'html',
                data: {formKey: window.FORM_KEY},
                method: 'GET',
                success: function (html) {
                    this.that = that; // to be used in okAction() functions
                    $('.integration-popup-container').html(html);
                    $('.integration-popup-container').dialog({
                        title: that.options.name,
                        modal: true,
                        autoOpen: true,
                        minHeight: 450,
                        minWidth: dialogOptions.minWidth,
                        dialogClass: 'integration-dialog',
                        position: {at: 'top+25%'},
                        buttons: [
                            {
                                text: $.mage.__('Cancel'),
                                click: function() {
                                    $(this).dialog("close");
                                }
                            },
                            {
                                text: dialogOptions.okButtonLabel,
                                'class': 'primary',
                                click: function () {
                                    switch (that.options.dialog) {
                                        case 'permissions':
                                            $(this).dialog('destroy');
                                            that.options.dialog = 'tokens';
                                            that._showPopup();
                                            break;
                                        case 'tokens':
                                            window.alert('Not implemented');
                                            break;
                                    }
                                }
                            }
                        ]
                    }).bind(this);
                }
            });
        },
    });
})(jQuery);
