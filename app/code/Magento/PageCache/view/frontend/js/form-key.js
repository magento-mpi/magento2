/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    /**
     * FormKey Widget - this widget is generating from key, saves it to cookie and
     */
    $.widget('mage.formKey', {

        options: {
            inputSelector: 'input[name="form_key"]',
            allowedCharacters: '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            length: 16
        },

        _create: function() {
            var formKey = $.cookie('form_key');
            if (!formKey) {
                formKey = this._generate(this.options.allowedCharacters, this.options.length);
                var date = new Date();
                date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
                $.cookie('form_key', formKey, {expires: date, path: '/'});
            }
            $(this.options.inputSelector).val(formKey);
        },

        _generate: function(chars, length) {
            var result = '';
            for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
            return result;
        }
    });
})(jQuery);