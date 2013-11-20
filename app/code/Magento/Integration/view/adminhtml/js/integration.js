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
            url: '', // ex.: http://.../integration/activate/id/1
            name: '' // Integration name
        },

        _create: function ()
        {
            this._on({'click': '_showPermissionPopup'});
        },

        _showPermissionPopup: function ()
        {
            this._showPopup('permissions', 'Allow');
        },

        _showTokenPopup: function ()
        {
            this._showPopup('tokens', 'Activate');
        },

        _showPopup: function (dialog, okButtonLabel)
        {
            if (['permissions', 'deactivate', 'reauthorize', 'tokens'].indexOf(dialog) === -1) {
                throw 'Invalid dialog type';
            }

            var that = this;
            jQuery.ajax({
                url: this.options.url + '?popup_dialog=' + dialog,
                showLoader: true,
                cache: false,
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
                        minWidth: 600,
                        dialogClass: 'integration-dialog',
                        position: {at: 'center'},
                        buttons: [
                            {
                                text: $.mage.__('Cancel'),
                                click: function() {
                                    $(this).dialog("close");
                                }
                            },
                            {
                                text: $.mage.__(okButtonLabel),
                                'class': 'primary',
                                click: function () {
                                    switch (dialog) {
                                        case 'permissions':
                                            $(this).dialog('destroy');
                                            that._showTokenPopup();
                                            break;
                                        case 'tokens':
                                            window.alert('Not implemented');
                                            break;
                                    }
                                }
                            }
                        ]
                    });
                }
            });
        },
    });
})(jQuery);
