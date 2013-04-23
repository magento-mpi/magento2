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

            var products = $('thead td', this.element);

            if (products.length > this.options.productsInRow) {
                var headings = $('<table></table>').addClass('comparison headings data table').insertBefore(this.element.closest('.container'));
                this.element.addClass('scroll');
                $('th',this.element).each(function(){
                    var some = $(this).clone();
                    var height = $(this).height();
                    some.css('height', function() {
                        return height;
                    });

                    $(some).appendTo(headings);
                    $(some).wrap('<tr />');
                    //this.hide();
                });
            }

            $(this.options.windowPrintSelector).on('click', function(e) {
                e.preventDefault();
                window.print();
            });

            $.each(this.options.selectors, function(i, selector) {
                $(selector).on('click', function(e) {
                    e.preventDefault();
                    window.location.href = $(this).data('url');
                });
            });

        }
    });
})(jQuery, window);
