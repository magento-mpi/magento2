/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
define([
    "jquery",
    "jquery/ui",
    "mage/decorate"
], function($){
    "use strict";
 
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
            trackInfoTable:'#track-info-table',
            trackInfoTbody: '#track-info-tbody'
        },

        /**
         * Initialize and attach event callbacks for adding and deleting RMA tracking rows
         * @private
         */
        _create: function() {
            var self = this;
            self.element.trigger('mage.setUpRmaOptions', self);
            $(this.options.trackInfoTable).decorate('table');
            $(this.options.addTrackNumberBtnId).on('click', $.proxy(self._addTrackNumber, self));
            $(this.options.trackInfoTbody).on('click', 'a[data-entity-id]', function(e) {
                e.preventDefault();
                self._deleteTrackNumber.call(self, $(this).data("entity-id"));
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
         * @private
         * @param number
         */
        _deleteTrackNumber: function(number) {
            if (window.confirm(this.options.deleteMsg)) {
                $.proxy(this._poster(this.options.deleteLabelUrl, {number: number}), this);
            }

        },

        /**
         * Helper ajax method to post to a given url with the provided data
         * updating the markup with the return html response
         * @private
         * @param url
         * @param data
         */
        _poster: function(url, data) {
            var rmaPleaseWait = $(this.options.rmaPleaseWait);
            var trackInfoTbody = $(this.options.trackInfoTbody);
            var trackInfoTable = $(this.options.trackInfoTable);
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
                    trackInfoTbody.html(data).trigger('contentUpdated');
                },
                complete: function() {
                    rmaPleaseWait.hide();
                    trackInfoTable.decorate('table');
                }
            });
        }
    });

    return $.mage.rmaTrackInfo;
});