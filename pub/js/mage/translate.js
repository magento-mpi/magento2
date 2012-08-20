/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */

var Translate = Class.create();
Translate.prototype = {
    initialize: function(data){
        this.data = $H(data);
    },

    translate : function(){
        var args = arguments;
        var text = arguments[0];

        if(this.data.get(text)){
            return this.data.get(text);
        }
        return text;
    },
    add : function() {
        if (arguments.length > 1) {
            this.data.set(arguments[0], arguments[1]);
        } else if (typeof arguments[0] =='object') {
            $H(arguments[0]).each(function (pair){
                this.data.set(pair.key, pair.value);
            }.bind(this));
        }
    }
};


(function($) {
    $.mage = $.mage || {};
    $.extend($.mage, {
        translate: new (function(){
            var _data = {};
            this.add = function() {
                if (arguments.length > 1) {
                    _data[arguments[0]] = arguments[1];
                } else if (typeof arguments[0] =='object') {
                    $.extend(_data, arguments[0]);
                }
            };
            this.translate = function(text) {
                return _data[text] ? _data[text] : text;
            }
        })
    });
    $.mage.__ = $.proxy($.mage.translate.translate, $.mage.translate);
})(jQuery);
