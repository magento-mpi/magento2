/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
InfiniteScroll = TestCase('InfiniteScroll');
InfiniteScroll.prototype.testInit = function() {
    jQuery(window).infinite_scroll({url: ''});
    assertEquals(true, jQuery(window).is(':vde-infinite_scroll'));
    jQuery(window).infinite_scroll('destroy');
};
