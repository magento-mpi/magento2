/**
 * {license_notice}
 *
 * @category    GiftWrapping
 * @package     enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    "use strict";

    $.widget('mage.giftWrapping', {

        /**
         * options with default values for setting up the template
         */
        options: {
            //Template containers for Gift wrapping designs and Options
            templateWrapping: '#gift-wrapping-container',
            templateOptions: '#gift-options-container',
            //These options are the Design information set from the backend
            designsInfo: null,
            itemsInfo: null,
            cardInfo: null,
            allowForOrder: null,
            allowGiftReceipt: null,
            allowPrintedCard: null,
            imgBoxSrc: null,
            //Container ids and classes
            addPrintedCardPrefix: "add-printed-card-",
            noDisplayClass: "no-display",
            giftWrappingSelectPrefix: "giftwrapping-",
            imageBoxSelectorPrefix: '#image-box-',
            priceBoxSelectorPrefix: '#price-box-',
            orderContainerPrefix: 'options-order-container',
            optionsItemContainerPrefix: 'options-items-container',
            extraOptionsContainerPrefix: 'extra-options-container',
            addGiftOptionSelectorPrefix: '#add-gift-options-',
            giftOptionsOrderSelectorPrefix: '#add-gift-options-for-order-',
            giftItemsOrderSelectorPrefix: '#add-gift-options-for-items-',
            //Price related selectors
            priceInclTaxSelectorPrefix: '#price-including-tax-',
            priceExclTaxSelectorPrefix: '#price-excluding-tax-',
            regPriceSelectorPrefix: '#regular-price-',
            priceOptionSelectorPrefix: "#price-options-box-"
        },

        /**
         * Initialize gift wrapping containers
         * @private
         */
        _create: function() {
            $($.proxy(this._ready, this));
            if (this.options.allowPrintedCard) {
                this.element.on('click', 'input:checkbox[id^="' + this.options.addPrintedCardPrefix + '"]', this.options, this.showCardPrice);
            }
            this.element.on('change', 'select[id^="' + this.options.giftWrappingSelectPrefix + '"]', this, this.setWrapping);
        },

        /**
         * Enable all gift wrapping options based on widget initialization parameters
         * Invoke gift wrapping template processing functions
         * @private
         */
        _ready: function() {
            if (this.options.allowForOrder) {
                this.processGiftOptions();
            }
            this.processGiftOptionItems();
            if (this.options.allowGiftReceipt || this.options.allowPrintedCard) {
                this.processGiftReceiptCardOptions();
            }
        },

        /**
         * Process and generate gift options/design block for the entire order
         * @public
         * @param {Object} instance - widget instance
         */
        processGiftOptions: function() {
            var data = {},
                instance = this;
            $('.' + instance.options.orderContainerPrefix).each(function() {
                data.id = this.id.replace(instance.options.orderContainerPrefix + '-', '');
                data.addrId = false;
                instance.insertBlock(this, data);
                $(instance.options.addGiftOptionSelectorPrefix + data.id + ', ' + instance.options.giftOptionsOrderSelectorPrefix + data.id)
                    .removeClass(instance.options.noDisplayClass);
            });
        },

        /**
         * Process and render gift options/design block for individuals order items
         * @public
         * @param {Object} instance - widget instance
         */
        processGiftOptionItems: function() {
            var data = {},
                instance = this;
            $('.' + instance.options.optionsItemContainerPrefix).each(function() {
                var idArr = this.id.replace(instance.options.optionsItemContainerPrefix + '-', '').split('-'),
                    id = idArr[1];
                if (instance.options.itemsInfo[id]) {
                    data.id = id;
                    data.addrId = idArr[0];
                    instance.insertBlock(this, data);
                    $(instance.options.addGiftOptionSelectorPrefix + idArr[0] + ', ' + instance.options.giftItemsOrderSelectorPrefix + idArr[0])
                        .removeClass(instance.options.noDisplayClass);
                }
            });
        },

        /**
         * Template processing for additional options : Printed Card and Gift Receipt
         * @public
         * @param {Object} instance - widget instance
         */
        processGiftReceiptCardOptions: function() {
            var instance = this;
            $('.' + instance.options.extraOptionsContainerPrefix).each(function() {
                var id = this.id.replace(instance.options.extraOptionsContainerPrefix + '-', ''),
                    cardInfo = instance.options.cardInfo[id];
                if (cardInfo) {
                    cardInfo.id = id;
                } else {
                    cardInfo = {"id": id};
                }
                instance.insertOptions(this, cardInfo);
                $(instance.options.addGiftOptionSelectorPrefix + id).removeClass(instance.options.noDisplayClass);
            });
        },

        /**
         * Create and insert Gift wrapping option/design block
         * @public
         * @param {Object} element - container for template block
         * @param {Object} data - substitution data
         */
        insertBlock: function(element, data) {
            this._processTemplate(this.options.templateWrapping, element, [
                {_id_: data.id, _blockId_: data.addrId}
            ]);
        },

        /**
         * Create and insert Gift wrapping additional options
         * @public
         * @param {Object} element - container for template block
         * @param {Object} data - substitution data
         */
        insertOptions: function(element, data) {
            this._processTemplate(this.options.templateOptions, element, [data]);
        },

        /**
         * Utility to process templates
         * @private
         * @param {String} templateSelector
         * @param {Object} element - container
         * @param {Object} data - template data
         */
        _processTemplate: function(templateSelector, element, data) {
            var $tmpl = $(templateSelector);
            if ($tmpl.length && element && data) {
                $tmpl.tmpl(data).appendTo(element);
            }
        },

        /**
         * This is the event handler to handle the change event for the gift design select box
         * @public
         * @param {Object} e - event object
         */
        setWrapping: function(e) {
            var instance = e.data,
                $this = $(this),
                designLayer = $this.siblings('div'),
                designBlockId = $this.prop('id').replace(instance.options.giftWrappingSelectPrefix, ''),
                blockId = $this.data("addrId"),
                designInfo = instance.options.designsInfo[this.value];

            //If a design is selected in the drop down, render it with price
            if (this.value) {
                instance.setDesign(designInfo.path, designLayer.find('img'))
                    .setPrice(instance.options.itemsInfo[designBlockId], designInfo, designBlockId, blockId);
            }
            //Based on design selection toggle design layer display
            designLayer.toggleClass(instance.options.noDisplayClass, !this.value);
        },

        /**
         * Set the image path based on provided source
         * @public
         * @param {String} path - path to the design image
         * @param {Object} $img - jQuery design image object
         * @return {Object} context for chaining
         */
        setDesign: function(path, $img) {
            $img.prop("src", path ? path : this.options.imgBoxSrc);
            return this;
        },

        /**
         * Function to handle Design pricing
         * @public
         * @param {Object} itemsInfo - Individual item information object containing price attributes
         * @param {Object} designInfo - Individual design information object containing price attributes
         * @param {String} designBlockId - design block id
         * @param {String} addressId - quote address item id
         * @return {Object} context for chaining
         */
        setPrice: function(itemsInfo, designInfo, designBlockId, addrId) {
            var blockId = parseInt(addrId, 10) ? addrId : designBlockId,
                price = itemsInfo && itemsInfo.price ? itemsInfo.price : designInfo[blockId].price,
                priceInclTax = itemsInfo && itemsInfo.price_incl_tax ? itemsInfo.price_incl_tax : designInfo[blockId].price_incl_tax,
                priceExclTax = itemsInfo && itemsInfo.price_excl_tax ? itemsInfo.price_excl_tax : designInfo[blockId].price_excl_tax;

            if (price || (priceInclTax && priceExclTax)) {
                $(this.options.priceInclTaxSelectorPrefix + designBlockId).text(priceInclTax);
                $(this.options.priceExclTaxSelectorPrefix + designBlockId).text(priceExclTax);
                $(this.options.regPriceSelectorPrefix + designBlockId).text(price);
            }
            return this;
        },

        /**
         * This is the handler function to handle the checkbox toggle additional gift option for Printed Card
         * @public
         * @param {Object} e - event object
         */
        showCardPrice: function(e) {
            $(e.data.priceOptionSelectorPrefix + this.id.replace(e.data.addPrintedCardPrefix, ''))
                .toggleClass(e.data.noDisplayClass, !this.checked);
        }

    });
})(jQuery);
