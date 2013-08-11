/**
 * {license_notice}
 *
 * @category    mage compare list
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($, window) {
    $.widget('mage.compareList', {
        _create: function() {
            this.element.decorate('table');

            $(this.options.windowCloseSelector).on('click', function() {
                window.close();
            });

            $(this.options.windowPrintSelector).on('click', function(e) {
                e.preventDefault();
                window.print();
            });

            var ajaxSpinner = $(this.options.ajaxSpinner);
            $(this.options.productRemoveSelector).on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(e.target).data('url'),
                    type: 'POST',
                    beforeSend: function() {
                        ajaxSpinner.show();
                    }
                }).done(function() {
                    ajaxSpinner.hide();
                    window.location.reload();
                    window.opener.location.reload();
                });
            });

            $.each(this.options.selectors, function(i, selector) {
                $(selector).on('click', function(e) {
                    e.preventDefault();
                    window.opener.focus();
                    window.opener.location.href = $(this).data('url');
                });
            });
        }
    });
})(jQuery, window);
