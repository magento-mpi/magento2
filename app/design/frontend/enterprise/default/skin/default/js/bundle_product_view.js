/**
 * Special bundle product view page. Available in this theme only.
 *
 * {license_notice}
 *
 * @category    design
 * @package     enterprise_default
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!window.Enterprise) {
    window.Enterprise = {};
}

Enterprise.templatesPattern =  /(^|.|\r|\n)(\{\{(.*?)\}\})/;

Enterprise.Bundle = {
     oldReloadPrice: false,
     initialize: function () {
        this.slider = $('bundleProduct');
        this.xOffset = $('bundle-product-wrapper').getDimensions().width;
     },
     swapReloadPrice: function () {
         Enterprise.Bundle.oldReloadPrice = Product.Bundle.prototype.reloadPrice;
         Product.Bundle.prototype.reloadPrice = Enterprise.Bundle.reloadPrice;
         Product.Bundle.prototype.selection = Enterprise.Bundle.selection;
     },
     reloadPrice: function () {
         var result = Enterprise.Bundle.oldReloadPrice.bind(this)();
         var priceContainer, duplicateContainer = null
         if (priceContainer = $('bundle-product-wrapper').down('.price-box .price-as-configured')) {
            if (duplicateContainer = $('bundle-product-wrapper').down('.duplicate-price-box .price-as-configured')) {
                duplicateContainer.down('.price').update(
                    priceContainer.down('.price').innerHTML
                );
            }
         }
         if (!this.summaryTemplate && $('bundle-summary-template')) {
             this.summaryTemplate = new Template($('bundle-summary-template').innerHTML, Enterprise.templatesPattern);
             this.optionTemplate = new Template($('bundle-summary-option-template').innerHTML, Enterprise.templatesPattern);
             this.optionMultiTemplate = new Template($('bundle-summary-option-multi-template').innerHTML, Enterprise.templatesPattern);
         }

         if (this.summaryTemplate && $('bundle-summary')) {
             var summaryHTML = '';
             for (var option in this.config.options) {
                if (typeof (this.config.selected[option]) !== 'undefined') {
                    var optionHTML = '';
                    for (var i = 0, l = this.config.selected[option].length; i < l; i ++) {
                        var selection = this.selection(option, this.config.selected[option][i]);
                        if (selection && this.config.options[option].isMulti) {
                            optionHTML += this.optionMultiTemplate.evaluate(selection);
                        } else if (selection) {
                            optionHTML += this.optionTemplate.evaluate(selection);
                        }
                    }

                    if (optionHTML.length > 0) {
                        summaryHTML += this.summaryTemplate.evaluate({label:this.config.options[option].title.escapeHTML(), options: optionHTML});
                    }
                }
             }

             if (typeof($('bundle-summary').update(summaryHTML).childElements().last()) != 'undefined') {
                 $('bundle-summary').update(summaryHTML).childElements().last().addClassName('last');
             }
         }
         return result;
     },
     selection: function(optionId, selectionId) {
        if (selectionId == '' || selectionId == 'none') {
            return false;
        }
        var qty = null;
        if (this.config.options[optionId].selections[selectionId].customQty == 1 && !this.config['options'][optionId].isMulti) {
            if ($('bundle-option-' + optionId + '-qty-input')) {
                qty = $('bundle-option-' + optionId + '-qty-input').value;
            } else {
                qty = 1;
            }
        } else {
            qty = this.config.options[optionId].selections[selectionId].qty;
        }

        return {qty: qty, name: this.config.options[optionId].selections[selectionId].name.escapeHTML()};
     },
     start: function () {
        if (!$('bundle-product-wrapper').hasClassName('moving-now')) {
            new Effect.Move(this.slider, {
                x: -this.xOffset, y: 0, mode: 'relative', duration: 1.5,
                beforeStart: function (effect) {
                    $('bundle-product-wrapper').setStyle({height: $('productView').getHeight() + 'px'});
                    $('options-container').show();
                    Enterprise.BundleSummary.initialize();
                    $('bundle-product-wrapper').addClassName('moving-now');
                },
                afterFinish: function (effect) {
                    $('bundle-product-wrapper').setStyle({height: 'auto'});
                    $('productView').hide();
                    $('bundle-product-wrapper').removeClassName('moving-now');
                }
            });
         }
     },
     end: function () {
        if (!$('bundle-product-wrapper').hasClassName('moving-now')) {
            new Effect.Move(this.slider, {
                    x: this.xOffset, y: 0, mode: 'relative', duration: 1.5,
                    beforeStart: function (effect) {
                        $('bundle-product-wrapper').setStyle({height: $('options-container').getHeight() + 'px'});
                        $('productView').show();
                        $('bundle-product-wrapper').addClassName('moving-now');
                    },
                    afterFinish: function (effect) {
                        $('bundle-product-wrapper').setStyle({height: 'auto'});
                        $('options-container').hide();
                        Enterprise.BundleSummary.exitSummary();
                        $('bundle-product-wrapper').removeClassName('moving-now');
                    }
                });
        }
     }
};

Enterprise.BundleSummary = {
    initialize: function () {
        this.summary = $('bundleSummary');
        this.summaryOffsetTop = $('customizeTitle').getDimensions().height;
        this.summary.setStyle({top:this.summaryOffsetTop + "px"});
        this.summaryContainer = this.summary.up(0);
        this.doNotCheck = false;
        this.summaryStartY = this.summary.positionedOffset().top;
        this.summaryStartY = this.summaryOffsetTop;
        this.summaryStartX = this.summary.positionedOffset().left;
        this.onDocScroll = this.handleDocScroll.bindAsEventListener(this);
        this.GetScroll = setInterval(this.onDocScroll, 50);
        this.onEffectEnds = this.effectEnds.bind(this);
    },

    handleDocScroll: function () {
        if (this.currentOffsetTop == document.viewport.getScrollOffsets().top
            && (this.checkOffset(null) == null)) {
            return;
        } else {
            if (this.currentOffsetTop == document.viewport.getScrollOffsets().top) {
                this.doNotCheck = true;
            }
            this.currentOffsetTop = document.viewport.getScrollOffsets().top;
        }

        if (this.currentEffect) {
            this.currentEffect.cancel();
            var topOffset = 0;
            if (this.summaryContainer.viewportOffset().top < -60) {
               topOffset =  -(this.summaryContainer.viewportOffset().top);
            } else {
               topOffset = this.summaryStartY;
            }

            topOffset = this.checkOffset(topOffset);
            if (topOffset === null) {
                this.currentEffect = false;
                return;
            }

            this.currentEffect.start({
                x: this.summaryStartX,
                y: topOffset,
                mode: 'absolute',
                duration: 0.3,
                afterFinish: this.onEffectEnds
            });



            return;
        }


        this.currentEffect = new Effect.Move(this.summary);
    },

    effectEnds: function () {
        if (this.doNotCheck == true) {
            this.doNotCheck = false;
        }
    },

    checkOffset: function (offset) {
        if (this.doNotCheck && offset === null) {
            return null;
        }
        var dimensions = this.summary.getDimensions();
        var parentDimensions = this.summary.up().getDimensions();
        if ((offset !== null ? offset : this.summary.offsetTop) + dimensions.height >= parentDimensions.height) {
            offset = parentDimensions.height - dimensions.height;
        } else if (offset === null &&
            this.currentOffsetTop > (this.summaryContainer.viewportOffset().top) &&
            (this.currentOffsetTop - this.summaryContainer.viewportOffset().top) > this.summary.offsetTop) {
            offset = this.currentOffsetTop - this.summaryContainer.viewportOffset().top;
        }


        return offset;
    },

    exitSummary: function () {
        clearInterval(this.GetScroll);
    }
};
