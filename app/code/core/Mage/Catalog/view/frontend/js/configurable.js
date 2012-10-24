/**
 * {license_notice}
 *
 * @category    Varien
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**************************** CONFIGURABLE PRODUCT **************************/
/*global Product:true Option:true window:true*/
/*jshint jquery:true*/
(function ($) {
    $(function ($, undefined) {
        var productConfigProcessor = function (spConfig, optionsPrice) {
            var self = this, i = 0;
            this.init = function () {
                self.taxConfig = spConfig.taxConfig;
                if (self.containerId) {
                    self.$setings = $('#' + spConfig.containerId + ' ' + '.super-attribute-select');
                } else {
                    self.$setings = $('.super-attribute-select');
                }
                self.state = {};
                self.priceTemplate = spConfig.template;
                self.prices = spConfig.prices;

                // Set default values from config
                if (spConfig.defaultValues) {
                    self.values = spConfig.defaultValues;
                }

                // Overwrite defaults by url
                var separatorIndex = window.location.href.indexOf('#');
                if (separatorIndex !== -1) {
                    var paramsStr = window.location.href.substr(separatorIndex + 1);
                    var urlValues = paramsStr.toQueryParams();
                    if (!self.values) {
                        self.values = {};
                    }

                    for (i = 0; i < urlValues.length; i++) {
                        self.values[i] = urlValues[i];
                    }
                }

                // Overwrite defaults by inputs values if needed
                if (spConfig.inputsInitialized) {
                    self.values = {};
                    $.each(self.$setings, function (element) {
                        if (element.value) {
                            var attributeId = element.id.replace(/[a-z]*/, '');
                            self.values[attributeId] = element.value;
                        }
                    });
                }

                // Put events to check select reloads
                $.each(self.$setings, function (index, element) {
                    $(element).on('change', self.configure);
                });

                // fill state
                $.each(self.$setings, function (index, element) {
                    var attributeId = element.id.replace(/[a-z]*/, '');
                    if (attributeId && spConfig.attributes[attributeId]) {
                        element.config = spConfig.attributes[attributeId];
                        element.attributeId = attributeId;
                        self.state[attributeId] = false;
                    }
                });

                // Init $setings dropdown
                var childSettings = [];
                for (i = self.$setings.length - 1; i >= 0; i--) {
                    var prevSetting = self.$setings[i - 1] ? self.$setings[i - 1] : false;
                    var nextSetting = self.$setings[i + 1] ? self.$setings[i + 1] : false;
                    if (i === 0) {
                        self.fillSelect(self.$setings[i]);
                    } else {
                        self.$setings[i].disabled = true;
                    }
                    /*
                     Need to investigate cloning
                     */
                    self.$setings[i].childsetings = childSettings.slice(0);
                    self.$setings[i].prevSetting = prevSetting;
                    self.$setings[i].nextSetting = nextSetting;
                    childSettings.push(self.$setings[i]);
                }

                // Set values to inputs
                self.configureForValues();
                //document.observe("dom:loaded", self.configureForValues);
            };

            this.configureForValues = function () {
                if (self.values) {
                    self.$setings.each(function (element) {
                        var attributeId = element.attributeId;
                        element.value = (typeof(self.values[attributeId]) === 'undefined') ? '' : self.values[attributeId];
                        self.configureElement(element);
                    });
                }
            };

            this.configure = function () {
                self.configureElement(this);
            };

            this.configureElement = function (element) {
                self.reloadOptionLabels(element);
                if (element.value) {
                    self.state[element.config.id] = element.value;
                    if (element.nextSetting) {
                        element.nextSetting.disabled = false;
                        self.fillSelect(element.nextSetting);
                        self.resetChildren(element.nextSetting);
                    }
                }
                else {
                    self.resetChildren(element);
                }
                self.reloadPrice();
            };

            this.reloadOptionLabels = function (element) {
                var selectedPrice = 0;
                if (element.options[element.selectedIndex].config && !spConfig.stablePrices) {
                    selectedPrice = parseFloat(element.options[element.selectedIndex].config.price);
                }
                for (var i = 0; i < element.options.length; i++) {
                    if (element.options[i].config) {
                        element.options[i].text = self.getOptionLabel(element.options[i].config, element.options[i].config.price - selectedPrice);
                    }
                }
            };

            this.resetChildren = function (element) {
                if (element.childsetings) {
                    for (i = 0; i < element.childsetings.length; i++) {
                        element.childsetings[i].selectedIndex = 0;
                        element.childsetings[i].disabled = true;
                        if (element.config) {
                            self.state[element.config.id] = false;
                        }
                    }
                }
            };

            this.fillSelect = function (element) {
                var attributeId = element.id.replace(/[a-z]*/, '');
                var options = self.getAttributeOptions(attributeId);
                self.clearSelect(element);
                element.options[0] = new Option('', '');
                element.options[0].innerHTML = spConfig.chooseText;

                var prevConfig = false;
                if (element.prevSetting) {
                    prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
                }

                if (options) {
                    var index = 1;
                    for (var i = 0; i < options.length; i++) {
                        var allowedProducts = [];
                        if (prevConfig) {
                            for (var j = 0; j < options[i].products.length; j++) {
                                if (prevConfig.config.allowedProducts && prevConfig.config.allowedProducts.indexOf(options[i].products[j]) > -1) {
                                    allowedProducts.push(options[i].products[j]);
                                }
                            }
                        } else {
                            allowedProducts = options[i].products.slice(0);
                        }

                        if (allowedProducts.size() > 0) {
                            options[i].allowedProducts = allowedProducts;
                            element.options[index] = new Option(self.getOptionLabel(options[i], options[i].price), options[i].id);
                            if (typeof options[i].price !== 'undefined') {
                                element.options[index].setAttribute('price', options[i].price);
                            }
                            element.options[index].config = options[i];
                            index++;
                        }
                    }
                }
            };

            this.getOptionLabel = function (option, price) {
                price = parseFloat(price);
                var tax, incl, excl;
                if (self.taxConfig.includeTax) {
                    tax = price / (100 + self.taxConfig.defaultTax) * self.taxConfig.defaultTax;
                    excl = price - tax;
                    incl = excl * (1 + (self.taxConfig.currentTax / 100));
                } else {
                    tax = price * (self.taxConfig.currentTax / 100);
                    excl = price;
                    incl = excl + tax;
                }

                if (self.taxConfig.showIncludeTax || self.taxConfig.showBothPrices) {
                    price = incl;
                } else {
                    price = excl;
                }

                var str = option.label;
                if (price) {
                    if (self.taxConfig.showBothPrices) {
                        str += ' ' + self.formatPrice(excl, true) + ' (' + self.formatPrice(price, true) + ' ' + self.taxConfig.inclTaxTitle + ')';
                    } else {
                        str += ' ' + self.formatPrice(price, true);
                    }
                }
                return str;
            };

            this.formatPrice = function (price, showSign) {
                var str = '';
                price = parseFloat(price);
                if (showSign) {
                    if (price < 0) {
                        str += '-';
                        price = -price;
                    }
                    else {
                        str += '+';
                    }
                }

                var roundedPrice = (Math.round(price * 100) / 100).toString();

                if (self.prices && self.prices[roundedPrice]) {
                    str += self.prices[roundedPrice];
                }
                else {
                    str += self.priceTemplate.replace(/#\{(.*?)\}/, price.toFixed(2));
                }
                return str;
            };

            this.clearSelect = function (element) {
                for (var i = element.options.length - 1; i >= 0; i--) {
                    element.remove(i);
                }
            };

            this.getAttributeOptions = function (attributeId) {
                if (spConfig.attributes[attributeId]) {
                    return spConfig.attributes[attributeId].options;
                }
            };

            this.reloadPrice = function () {
                if (spConfig.disablePriceReload) {
                    return;
                }
                var price = 0;
                var oldPrice = 0;
                for (i = self.$setings.length - 1; i >= 0; i--) {
                    var selected = self.$setings[i].options[self.$setings[i].selectedIndex];
                    if (selected.config) {
                        price += parseFloat(selected.config.price);
                        oldPrice += parseFloat(selected.config.oldPrice);
                    }
                }

                optionsPrice.changePrice('config', {'price': price, 'oldPrice': oldPrice});
                optionsPrice.reloadPrice();

                return price;
            };
        };
        var spConfigData = {};
        $.mage.event.trigger("mage.spConfigData.initialize", spConfigData);
        /*
         Using the prototype class "Product.OptionsPrice". This has multiple reference and needs to be refactored
         as part of the Component/Class refactoring
         */
        (new productConfigProcessor(spConfigData.spConfig, spConfigData.priceOptionInstance)).init();
    });
})(jQuery);