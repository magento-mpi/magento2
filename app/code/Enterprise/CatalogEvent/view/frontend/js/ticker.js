/**
 * {license_notice}
 *
 * @category    frontend catalog event
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint browser:true jquery:true*/
(function($) {
    $.widget('mage.ticker', {
        options: {

        },

        _create: function() {
            this.start = new Date();
            setInterval($.proxy(function(){
                var seconds = this._getEstimate(),
                    daySec = Math.floor(seconds / (3600 * 24)) * (3600 * 24),
                    hourSec = Math.floor(seconds / 3600) * 3600,
                    minuteSec =  Math.floor(seconds / 60) * 60;
                this.element.find('[data-container="days"]').html(this._formatNumber(Math.floor(daySec / (3600 * 24))));
                this.element.find('[data-container="hour"]').html(this._formatNumber(Math.floor((hourSec - daySec) / 3600)));
                this.element.find('[data-container="minute"]').html(this._formatNumber(Math.floor((minuteSec - hourSec) / 60)));
                this.element.find('[data-container="second"]').html(this._formatNumber(seconds - minuteSec));
                if (daySec > 0) {
                    this.element.find('[data-container="second"]').prev('[data-container="delimiter"]').hide();
                    this.element.find('[data-container="second"]').hide();
                    this.element.find('[data-container="days"]').show();
                    this.element.find('[data-container="days"]').next('[data-container="delimiter"]').show();
                } else {
                    this.element.find('[data-container="days"]').hide();
                    this.element.find('[data-container="days"]').next('[data-container="delimiter"]').hide();
                    this.element.find('[data-container="second"]').prev('[data-container="delimiter"]').show();
                    this.element.find('[data-container="second"]').show();
                }
            }, this), 1000);
        },

        /**
         * get estimated remaining seconds
         * @returns {number}
         * @private
         */
        _getEstimate: function () {
            var now = new Date(),
                result = this.options.secondsToClose - (now.getTime() - this.start.getTime()) / 1000;
            return result < 0 ? 0 : Math.round(result);
        },

        /**
         * format number, prepend 0 for single digit number
         * @param number
         * @returns {string}
         * @private
         */
        _formatNumber: function (number) {
            return number < 10 ? '0' + number.toString() : number.toString();
        }
    });
})(jQuery);

