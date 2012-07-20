/**
 * {license_notice}
 *
 * @category    frontend newsletter
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($) {
    // Default value for menu
    var newsletterInit = {
        placeholderMessage: 'Enter your email address',
        errorClass: 'mage-error'
    };
    // Overwrite default showLabel method from jQuery validator to have fadeIn effect on error messages
    var extensionMethods = {
        showLabel: function (element, message) {
            var label = this.errorsFor(element);
            if (label.length) {
                // refresh error/success class
                label.removeClass(this.settings.validClass).addClass(this.settings.errorClass);

                // check if we have a generated label, replace the message then
                if (label.attr("generated")) {
                    label.hide().html(message).fadeIn('slow');

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
        // Trigger initalize event
        mage.event.trigger("mage.newsletter.initialize", newsletterInit);
        $(newsletterInit.newsletterId).mage().validate({errorClass: newsletterInit.errorClass});
        $(newsletterInit.newsletterInputId).on('click', function () {
            if ($(this).val() === newsletterInit.placeholderMessage) {
                $(this).val('');
            }
        });
        $(newsletterInit.newsletterInputId).on('focusout', function () {
            var inputField = $(this);
            // use setTimeout to make sure submit event happens before focusout event
            setTimeout(function () {
                if ($.trim(inputField.val()) === '') {
                    inputField.val(newsletterInit.placeholderMessage);
                }
            }, 400);
        });
    });
}(jQuery));