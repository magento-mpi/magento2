/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
InfiniteScroll = TestCase('InfiniteScroll');
InfiniteScroll.prototype.testInit = function() {
    jQuery(window).infinite_scroll({url: ''});
    assertEquals(true, !!jQuery(window).data('vdeInfinite_scroll'));
    jQuery(window).infinite_scroll('destroy');
};
