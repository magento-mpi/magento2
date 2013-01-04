/**
 * {license_notice}
 *
 * @category    mage.event
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
MageTest = TestCase('MageTest');

MageTest.prototype.setUp = function() {
    /*:DOC += <button id="save"></button>*/
};

MageTest.prototype.tearDown = function() {
    jQuery(jQuery.mage).off('buttoninit');
    jQuery('body').off('contentUpdated');
    jQuery.mage
        .component('tabs', null)
        .component('baseButton', null)
        .component('button', null);
};

MageTest.prototype.testInit = function() {
    /*:DOC += <button id="reset"></button>*/
    jQuery.mage.component('button', []);
    jQuery('button').mage('button');
    assertTrue(!!jQuery('#save').data('button'));
    assertTrue(!!jQuery('#reset').data('button'));
};

MageTest.prototype.testCustomizeOptions = function() {
    /*:DOC += <button id="reset"></button>*/
    jQuery.mage
        .component('button', [])
        .onInit('button', '#save', function(options) {
            options.disabled = false;
        });
    jQuery('button').mage('button', {disabled: true});
    assertTrue(!jQuery('#save').is(':disabled'));
    assertTrue(jQuery('#reset').is(':disabled'));
};

MageTest.prototype.testDataAttribute = function() {
    /*:DOC += <div id="main" data-mage-init="{button: [], tabs: []}"></div>*/
    jQuery.mage
        .component('button', [])
        .component('tabs', [])
        .init();
    assertTrue(!!jQuery('#main').data('button'));
    assertTrue(!!jQuery('#main').data('tabs'));
};

MageTest.prototype.testRejectComponent = function() {
    jQuery.mage
        .component('button', [])
        .onInit('button', function() {
            this.name = null;
        });
    jQuery('button').mage('button');
    assertTrue(!jQuery('#save').data('button'));
};

MageTest.prototype.testSubstituteComponent = function() {
    jQuery.mage
        .component('button', [])
        .component('tabs', [])
        .onInit('button', function() {
            this.name = 'tabs';
        });
    jQuery('button').mage('button');
    assertTrue(!jQuery('#save').data('button'));
    assertTrue(!!jQuery('#save').data('tabs'));
};

MageTest.prototype.testAddComponent = function() {
    expectAsserts(4);
    var resources = ['test1.js', 'test2.js'];
    jQuery.mage
        .component('button', resources)
        .onInit('button', '#save', function() {
            assertNotSame(resources, this.resources);
            assertEquals(resources.length, this.resources.length);
            jQuery.each(resources, jQuery.proxy(function(i, resource) {
                assertEquals(resource, this.resources[i]);
            }, this));
            this.name = null;
        });
    jQuery('button').mage('button');
};

MageTest.prototype.testExtendComponent = function() {
    expectAsserts(6);
    var baseButtonResources = ['test1.js', 'test2.js'],
        buttonResources = ['test3.js', 'test4.js'],
        resources = [];
    jQuery.merge(resources, baseButtonResources);
    jQuery.merge(resources, buttonResources);
    jQuery.mage
        .component('baseButton', baseButtonResources)
        .extend('button', 'baseButton', buttonResources)
        .onInit('button', '#save', function() {
            assertNotSame(resources, this.resources);
            assertEquals(resources.length, this.resources.length);
            jQuery.each(resources, jQuery.proxy(function(i, resource) {
                assertEquals(resource, this.resources[i]);
            }, this));
            this.name = null;
        });
    jQuery('button').mage('button');
};

MageTest.prototype.testContentUpdated = function() {
    jQuery.mage
        .component('tabs', [])
        .component('button', [])
        .init();
    jQuery('body')
        .append('<div id="test" data-mage-init="{tabs: []}">' +
            '<button data-mage-init="{button: []}"></button>' +
            '</div>');
    jQuery('#test').trigger('contentUpdated');
    assertTrue(!!jQuery('#test').data('tabs'));
    assertTrue(!!jQuery('#test button').data('button'));
};

MageTest.prototype.testArgumentsModification = function() {
    expectAsserts(3);
    var expected = ['some string', 5, true];
    jQuery.fn.testPlugin = function() {
        jQuery.each(arguments, function(i) {
            assertEquals(expected[i], this);
        });
        return this;
    };
    jQuery.mage
        .component('testPlugin', [])
        .onInit('testPlugin', function() {
            for (var i = this.args.length - 1; i >= 0; i--) {
                this.args[i] = expected[i];
            }
        });
    jQuery('#save').mage('testPlugin', 'test', 3, false);
    // teardown
    jQuery(jQuery.mage).off('testPlugininit');
    jQuery.mage.component('testPlugin', null);
};



/*
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
*/
