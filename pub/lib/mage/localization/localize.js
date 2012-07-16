/**
 * {license_notice}
 *
 * @category    localization
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint eqnull:true */
(function ($) {
    //closure localize object
    var localize = function (locale) {
        this.localize = Globalize;
        if (locale == null) {
            this.localize.culture('en');
        } else {
            this.localize.culture(locale);
        }
        this.dateFormat = ['d', 'D', 'f', 'F', 'M', 'S', 't', 'T', 'Y'];
        this.numberFormat = ['n', 'n1', 'n3', 'd', 'd2', 'd3', 'p', 'p1', 'p3', 'c', 'c0'];
    };
    localize.prototype.name = function () {
        return this.localize.culture().name;
    };
    localize.prototype.date = function (dateParam, format) {
        if ($.inArray(format.toString(), this.dateFormat) < 0) {
            return 'Invalid date formatter';
        }
        if (dateParam instanceof Date) {
            return this.localize.format(dateParam, format);
        }
        var d = new Date(dateParam.toString());
        if (d == null || d.toString === 'Invalid Date') {
            return d.toString;
        } else {
            return this.localize.format(d, format);
        }
    };
    localize.prototype.number = function (numberParam, format) {
        if ($.inArray(format.toString(), this.numberFormat)) {
            return 'Invalid number formatter';
        }
        if (typeof numberParam === 'number') {
            return this.localize.format(numberParam, format);
        }
        var num = Number(numberParam);
        if (num == null || isNaN(num)) {
            return numberParam;
        } else {
            return this.localize.format(num, format);
        }
    };
    localize.prototype.currency = function (currencyParam) {
        if (typeof currencyParam === 'number') {
            return this.localize.format(currencyParam, 'c');
        }
        var num = Number(currencyParam);
        if (num == null || isNaN(num)) {
            return currencyParam;
        } else {
            return this.localize.format(num, 'c');
        }
    };

    localize.prototype.translate = function (value) {
        var translatorData = {};
        translatorData.message = value;
        translatorData.translatedMessage = translateJson.hasOwnProperty(value) ? translateJson[value] : value;
        mage.event.trigger("mage.translate", translatorData);
        return translatorData.translatedMessage;
    };

    mage.locale = function (locale) {
        if (locale != null && locale.length > 0) {
            mage.localize = new localize(locale);
        } else {
            mage.localize = new localize();
        }
    };
    mage.locale($.cookie(mage.language.cookieKey) || mage.language.en);
}(jQuery));


