/**
 * {license_notice}
 *
 * @category    frontend product price option
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint evil:true browser:true jquery:true*/
(function ($) {
    $(document).ready(function() {
        // Default price option variables
        var priceOptionInit = {
            priceConfig: undefined, // This is a json generate from php helper with product price info
            optionConfig: undefined,// This is a json generate from php helper with option price info
            productCustomOptionSelector: '.product-custom-option'
        };
        $.mage.event.trigger("mage.priceOption.initialize", priceOptionInit);

        // A list of possible product price display elements
        var priceSelectors = [
            '#product-price-' + priceOptionInit.priceConfig.productId,
            '#bundle-price-' + priceOptionInit.priceConfig.productId,
            '#price-including-tax-' + priceOptionInit.priceConfig.productId,
            '#price-excluding-tax-' + priceOptionInit.priceConfig.productId,
            '#old-price-' + priceOptionInit.priceConfig.productId
        ];

        // Format price migrate from js.js
        function formatCurrency(price, format, showPlus){
            var precision = isNaN(format.requiredPrecision = Math.abs(format.requiredPrecision)) ? 2 : format.requiredPrecision;
            var integerRequired = isNaN(format.integerRequired = Math.abs(format.integerRequired)) ? 1 : format.integerRequired;
            var decimalSymbol = format.decimalSymbol === undefined ? "," : format.decimalSymbol;
            var groupSymbol = format.groupSymbol === undefined ? "." : format.groupSymbol;
            var groupLength = format.groupLength === undefined ? 3 : format.groupLength;
            var s = '';

            if (showPlus === undefined || showPlus === true) {
                s = price < 0 ? "-" : ( showPlus ? "+" : "");
            } else if (showPlus === false) {
                s = '';
            }
            var i = parseInt(price = Math.abs(+price || 0).toFixed(precision), 10) + '';
            var pad = (i.length < integerRequired) ? (integerRequired - i.length) : 0;
            while (pad) {
                i = '0' + i;
                pad--;
            }
            var j = i.length > groupLength ? i.length % groupLength : 0;
            var re = new RegExp("(\\d{" + groupLength + "})(?=\\d)", "g");

            /**
             * replace(/-/, 0) is only for fixing Safari bug which appears
             * when Math.abs(0).toFixed() executed on "0" number.
             * Result is "0.-0" :(
             */
            var r = (j ? i.substr(0, j) + groupSymbol : "") + i.substr(j).replace(re, "$1" + groupSymbol) +
                (precision ? decimalSymbol + Math.abs(price - i).toFixed(precision).replace(/-/, 0).slice(2) : "");
            var pattern = '';
            pattern = format.pattern.indexOf('{sign}') < 0 ? s + format.pattern : format.pattern.replace('{sign}', s);
            return pattern.replace('%s', r).replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        }

        // Scan all product custom options, calculate total option price and apply to product price
        function reloadPrice() {
            var optionPrice = {
                excludeTax: 0,
                includeTax: 0,
                oldPrice: 0,
                price: 0,
                update: function(price, excludeTax, includeTax, oldPrice) {
                    this.price += price;
                    this.excludeTax += excludeTax;
                    this.includeTax += includeTax;
                    this.oldPrice += oldPrice;
                }
            };
            $(priceOptionInit.productCustomOptionSelector).each(function() {
                var element = $(this);
                var optionIdStartIndex = element.attr('name').indexOf('[') + 1;
                var optionIdEndIndex = element.attr('name').indexOf(']');
                var optionId = parseInt(element.attr('name').substring(optionIdStartIndex, optionIdEndIndex), 10);
                if (priceOptionInit.optionConfig[optionId]) {
                    var configOptions = priceOptionInit.optionConfig[optionId];
                    if (element.attr('type') === 'checkbox' || element.attr('type') === 'radio') {
                        if (element.prop('checked')) {
                            if (configOptions[element.val()]) {
                                optionPrice.update(configOptions[element.val()].price,
                                    configOptions[element.val()].excludeTax,
                                    configOptions[element.val()].includeTax,
                                    configOptions[element.val()].oldPrice);
                            }
                        }
                    } else if (element.prop('tagName') === 'SELECT') {
                        $(element).find('option:selected').each(function() {
                            if (configOptions[$(this).val()]) {
                                optionPrice.update(configOptions[$(this).val()].price,
                                    configOptions[$(this).val()].excludeTax,
                                    configOptions[$(this).val()].includeTax,
                                    configOptions[$(this).val()].oldPrice);
                            }
                        });
                    } else if (element.prop('tagName') === 'TEXTAREA' || element.attr('type') === 'text') {
                        if (element.val()) {
                            optionPrice.update(configOptions.price, configOptions.excludeTax,
                                configOptions.includeTax, configOptions.oldPrice);
                        }
                    }
                }
            });
            var updatedPrice = {
                priceExclTax: optionPrice.excludeTax + priceOptionInit.priceConfig.priceExclTax,
                priceInclTax: optionPrice.includeTax + priceOptionInit.priceConfig.priceInclTax,
                productOldPrice: optionPrice.oldPrice + priceOptionInit.priceConfig.productOldPrice,
                productPrice: optionPrice.price + priceOptionInit.priceConfig.productPrice
            };
            // Loop through each priceSelector and update price
            $.each(priceSelectors, function(index, value) {
                var priceElement = $(value);
                if (priceElement.length === 1) {
                    var price = 0;
                    if (value.indexOf('price-including-tax-') >= 0) {
                        price = updatedPrice.priceInclTax;
                    } else if (value.indexOf('price-excluding-tax-') >= 0) {
                        price = updatedPrice.priceExclTax;
                    } else if (value.indexOf('old-price-') >= 0) {
                        if (priceOptionInit.priceConfig.showIncludeTax || priceOptionInit.priceConfig.showBothPrices) {
                            price = updatedPrice.priceInclTax;
                        } else {
                            price = updatedPrice.priceExclTax;
                        }
                    } else {
                        price = priceOptionInit.priceConfig.showIncludeTax ?
                            updatedPrice.priceInclTax : updatedPrice.priceExclTax;
                    }
                    priceElement.html(formatCurrency(price, priceOptionInit.priceConfig.priceFormat));
                    // If clone exists, update clone price as well
                    var clone = $(value + priceOptionInit.priceConfig.idSuffix);
                    if (clone.length === 1) {
                        clone.html(formatCurrency(price, priceOptionInit.priceConfig.priceFormat));
                    }
                }
            });
        }

        // Add click or change event to custom options
        $(priceOptionInit.productCustomOptionSelector).each(function() {
            var element = $(this);
            if (element.attr('type') === 'checkbox' || element.attr('type') === 'radio') {
                element.on('click', reloadPrice);
            }
            else if (element.prop('tagName') === 'SELECT' ||
                element.prop('tagName') === 'TEXTAREA' ||
                element.attr('type') === 'text') {
                element.on('change', reloadPrice);
            }
        });
    });
}(jQuery));