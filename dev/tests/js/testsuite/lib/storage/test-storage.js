/**
 * {license_notice}
 *
 * @category    mage.collapsible
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */

test('Storage', function() {
    expect(2);
    var key = 'test-storage';
    var storage = $.localStorage;
    if(window["localStorage"] !== null) {
        localStorage.setItem(key,'false');
        storage.set(key,'true');
        equal(localStorage.getItem(key),"true");
        equal(localStorage.getItem(key),storage.get(key));
    } else {
        $.cookie(key,'false');
        storage.set(key,'true');
        equal($.cookie(key),"true");
        equal($.cookie(key),storage.get(key));
    }
});