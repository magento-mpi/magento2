/**
 * {license_notice}
 *
 * @category    mage.design_editor
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
LoaderTest = TestCase('LoaderTest');
LoaderTest.prototype.testInit = function() {
    /*:DOC += <div id="loader"></div> */
    var loader = jQuery('#loader').loader();
    assertEquals(true, loader.is(':mage-loader'));
    loader.loader('destroy');
};
LoaderTest.prototype.testCreateOnBeforeSend = function() {
    /*:DOC += <div id="loader"></div> */
    var loader = jQuery('#loader').trigger('beforeSend.ajax');
    assertEquals(true, loader.is(':mage-loader'));
    loader.loader('destroy');
};
LoaderTest.prototype.testLoaderOnBody = function() {
    jQuery('body').loader();
    assertEquals(true, jQuery('body div:first').is('.loading-mask'));
    jQuery('body').loader('destroy');
};
LoaderTest.prototype.testLoaderOnDOMElement = function() {
    /*:DOC += <div id="loader"></div> */
    var loader = jQuery('#loader').loader();
    assertEquals(true, loader.prev().is('.loading-mask'));
    loader.loader('destroy');
};
LoaderTest.prototype.testLoaderOptions = function() {
    /*:DOC += <div id="loader"></div> */
    var loader = jQuery('#loader').loader({
        icon: 'icon.gif',
        texts: {
            loaderText: 'Loader Text',
            imgAlt: 'Image Alt Text'
        }
    });
    assertEquals('icon.gif', loader.prev().find('img').attr('src'));
    assertEquals('Image Alt Text', loader.prev().find('img').attr('alt'));
    assertEquals('Loader Text', loader.prev().find('span').text());
    loader.loader('destroy');
    loader.loader({
        template:'<div id="test-template"></div>'
    });
    assertEquals(true, loader.prev().is('#test-template'));
};
LoaderTest.prototype.testDestroyOnComplete = function() {
    /*:DOC += <div id="loader"></div> */
    var loader = jQuery('#loader').loader(),
        loaderExist = loader.is(':mage-loader');
    loader.trigger('complete.ajax');
    assertEquals(false, loader.is(':mage-loader') === loaderExist);
    loader.loader('destroy');
};
LoaderTest.prototype.testRender = function() {
    /*:DOC += <div id="loader" style="widht:200px; height:200px;"></div> */
    var loader = jQuery('#loader').loader();
    assertEquals(true, $('.loading-mask').is(':visible'));
    loader.loader('destroy');
};
LoaderTest.prototype.testShowHide = function() {
    /*:DOC += <div id="loader" style="widht:200px; height:200px;"></div> */
    var loader = jQuery('#loader').loader();
    loader.loader('show');
    assertEquals(true, $('.loading-mask').is(':visible'));
    loader.loader('hide');
    assertEquals(false, $('.loading-mask').is(':visible'));
    loader.loader('destroy');
};
LoaderTest.prototype.testDestroy = function() {
    /*:DOC += <div id="loader"></div> */
    var loader = jQuery('#loader').loader(),
        loaderExist = loader.is(':mage-loader');
    loader.loader('destroy');
    assertEquals(false, loader.is(':mage-loader') === loaderExist);
    loader.loader('destroy');
};