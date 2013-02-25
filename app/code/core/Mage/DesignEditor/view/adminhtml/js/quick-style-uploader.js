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

    var parentWidget = ($.blueimpFP || $.blueimp).fileupload;
    $.widget("vde.quickStyleUploader", parentWidget, {
        options: {
            dataType: 'json',
            replaceFileInput:  true,
            sequentialUploads: true,
            url:               null,
            remove_url:        null,
            uploader_id:       null,
            value:             null,
            container:         null,
            event:             null,

            /**
             * Add file
             * @param e
             * @param data
             */
            add: function (e, data) {
                data.submit();
            },

            /**
             * On done event
             * @param e
             * @param data
             */
            done: function (e, data) {
                if (data.result.error) {
                    alert(data.result.message);
                } else {
                    $(this).data('quickStyleUploader').setValue(data.result.content['name']);
                }
            },

            /**
             * Fail event
             * @param e
             * @param data
             */
            fail: function(e, data) {
                alert($.mage.__('File extension not known or unsupported type.'));
            }
        },

        /**
         * Init uploader
         * @param e
         * @param data
         */
        _init: function (e, data) {
            this._refreshControls();
        },

        /**
         * Remove file
         * @param event
         * @private
         */
        _remove: function (event) {
            $.ajax({
                type: 'POST',
                url:  this.options.remove_url,
                data: { file_name: this.options.value },
                dataType: 'json',
                success: $.proxy(function(response) {
                    if (response.error) {
                        alert($.mage.__('Error') + ': "' + response.message + '".');
                    } else {
                        $(this._prepareId(this.options.uploader_id + '-image')).remove();
                        this.setValue(null);
                    }
                }, this),
                error: function() {
                    alert($.mage.__('Error: unknown error.'));
                }
            });
        },

        /**
         * Set value
         * @param value
         */
        setValue: function (value) {
            this.options.value = value;
            this._refreshControls();
        },

        /**
         * Refresh controls
         * @protected
         */
        _refreshControls: function () {
            if (this.options.value) {
                var removeId = 'remove-button-' + Math.floor(Math.random() * 999);
                var progressTmpl = $(this._prepareId(this.options.uploader_id + '-template')).clone();
                progressTmpl.attr('id', this.options.uploader_id + '-image');
                var fileInfoHtml = progressTmpl.html().replace('{{name}}', this.options.value)
                    .replace('{{remove-id}}', removeId);
                progressTmpl.html(fileInfoHtml) ;
                progressTmpl.removeClass('no-display');
                progressTmpl.appendTo(this._prepareId(this.options.container));

                if (this.options.remove_url) {
                    $('#' + removeId).click($.proxy(this._remove, this));
                }
                $(this._prepareId(this.options.uploader_id + '-container')).addClass('no-display');
                $(this).trigger(this.options.event);
            } else {
                $(this._prepareId(this.options.uploader_id + '-container')).removeClass('no-display');
            }
        },

        /**
         * Escape id
         * @param id
         * @return {String}
         */
        _prepareId: function(id) {
            return document.getElementById(id);
        }
    });
})(jQuery);
