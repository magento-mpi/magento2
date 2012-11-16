/**
 * {license_notice}
 *
 * @category    Rma
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint evil:true browser:true jquery:true*/
(function($, window) {

    $.widget('mage.rmaTrackInfo', {

        /**
         * Default options
         * @type {Object}
         */
        options: {
            addTrackNumberBtnId: '#btn-add-track-number',
            trackingCarrierSelect: '#tracking_carrier_select',
            trackingNumberInput: '#tracking_number_input',
            rmaPleaseWait: '#rma-please-wait',
            trackInfoTbody: '#track-info-tbody'
        },

        /**
         * Initialize and attach event callbacks for adding and deleting RMA tracking rows
         * @private
         */
        _create: function() {
            var self = this;
            $(this.options.addTrackNumberBtnId).on('click', $.proxy(self._addTrackNumber, self));
            $(this.options.trackInfoTbody).on('click', 'a[data-entityid]', function(e) {
                e.preventDefault();
                self._deleteTrackNumber.call(self, $(this).data("entityid"));
            });
        },

        /**
         * Add new RMA tracking row
         * @private
         */
        _addTrackNumber: function() {
            if (this.element.validation().valid()) {
                $.proxy(this._poster(this.options.addLabelUrl, {
                    'carrier': $(this.options.trackingCarrierSelect).val(),
                    'number': $(this.options.trackingNumberInput).val()
                }), this);
            }
        },

        /**
         * Delete RMA tracking row for a given tracking number
         * @param number
         * @private
         */
        _deleteTrackNumber: function(number) {
            if (window.confirm(this.options.deleteMsg)) {
                $.proxy(this._poster(this.options.deleteLabelUrl, {number: number}), this);
            }

        },

        /**
         * Helper ajax method to post to a given url with the provided data
         * updating the markup with the return html response
         * @param url
         * @param data
         * @private
         */
        _poster: function(url, data) {
            var rmaPleaseWait = $(this.options.rmaPleaseWait);
            var trackInfoTbody = $(this.options.trackInfoTbody);
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'html',
                cache: false,
                data: data,
                beforeSend: function() {
                    rmaPleaseWait.show();
                },
                success: function(data) {
                    trackInfoTbody.html(data);
                },
                complete: function() {
                    rmaPleaseWait.hide();
                    //TODO:Need to be updated once the new decorator is finalized
                    window.decorateTable('track-info-table');
                }
            });
        }
    });

})(jQuery, window);