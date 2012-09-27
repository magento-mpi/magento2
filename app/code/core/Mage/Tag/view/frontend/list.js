/**
 * {license_notice}
 *
 * @category    tab
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
(function ($) {
    // Overwrite default showLabel method from jQuery validator to have fadeIn effect on error messages
    var extensionMethods = {
        showLabel: function (element, message) {
            var label = this.errorsFor(element);
            if (label.length) {
                // refresh error/success class
                label.removeClass(this.settings.validClass).addClass(this.settings.errorClass);

                // check if we have a generated label, replace the message then
                if (label.attr("generated")) {
                    label.hide().html(message);
                }
            } else {
                // create label
                label = $("<" + this.settings.errorElement + "/>")
                    .attr({"for": this.idOrName(element), generated: true})
                    .addClass(this.settings.errorClass)
                    .html(message || "").fadeIn('slow');
                if (this.settings.wrapper) {
                    // make sure the element is visible, even in IE
                    // actually showing the wrapped element is handled elsewhere
                    label = label.hide().show().wrap("<" + this.settings.wrapper + "/>").parent();
                }
                if (!this.labelContainer.append(label).length) {
                    if (this.settings.errorPlacement) {
                        this.settings.errorPlacement(label, $(element));
                    } else {
                        label.insertAfter(element);
                    }
                }
            }
            if (!message && this.settings.success) {
                label.text("");
                if (typeof this.settings.success === "string") {
                    label.addClass(this.settings.success);
                } else {
                    this.settings.success(label, element);
                }
            }
            this.toShow = this.toShow.add(label);
        }
    };
    $.extend(true, $.validator.prototype, extensionMethods);

    $(document).ready(function () {
        var _tag = {
            // Filled in initialization event
            formSelector: null
        };
        $.mage.event.trigger('mage.tag.initialize', _tag);
        $(_tag.formSelector).mage().validate({errorClass: 'mage-error', errorElement: 'div'});
    });
})(jQuery);
