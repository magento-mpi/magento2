/**
 * {license_notice}
 *
 * @category    mage.event
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
MageTest = TestCase('MageTest');
MageTest.prototype.testTrigger = function () {
    var observeFunc = function (e, o) {
        o.status = true;
    };
    $.mage.event.observe('mage.test.event', observeFunc);
    var obj = {status: false};
    assertEquals(false, obj.status);
    $.mage.event.trigger('mage.test.event', obj);
    assertEquals(true, obj.status);
    // Test removeObserver
    obj.status = false;
    assertEquals(false, obj.status);
    $.mage.event.removeObserver('mage.test.event', observeFunc);
    $.mage.event.trigger('mage.test.event', obj);
    assertEquals(false, obj.status);
};

MageTest.prototype.testLoad = function () {
    // Because the window load evnt already happened, syncQueue size already have 1 elements(the asyncLoad function)
    assertEquals(1, $.mage.load.js('test1'));
    assertEquals(1, $.mage.load.jsSync('test2'));
    assertEquals(1, $.mage.load.js('test1'));
    assertEquals(1, $.mage.load.jsSync('test2'));
};

MageTest.prototype.testLoadLanguage = function () {
    var mapping = {
        'localize': ['/pub/lib/mage/globalize/globalize.js',
            '/pub/lib/mage/globalize/cultures/globalize.culture.de.js',
            '/pub/lib/mage/localization/json/translate_de.js',
            '/pub/lib/mage/localization/localize.js']
    };
    assertEquals(1, $.mage.load.language('en', mapping));
    assertEquals(1, $.mage.load.language());
    assertEquals(5, $.mage.load.language('de', mapping));
};


