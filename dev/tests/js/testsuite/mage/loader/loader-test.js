/**
 * {license_notice}
 *
 * @category    mage.js
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
LoaderTest = TestCase('LoaderTest');
LoaderTest.prototype.setUp = function() {
    /*:DOC += <div id="loader"></div> */
};
LoaderTest.prototype.tearDown = function() {
    var loaderInstance = jQuery('#loader').data('loader');
    if(loaderInstance && loaderInstance.destroy) {
        loaderInstance.destroy();
    }
};
LoaderTest.prototype.getInstance = function() {
    return jQuery('#loader').data('loader');
};
LoaderTest.prototype.testInit = function() {
    var loader = jQuery('#loader').loader();
    loader.loader('show');
    assertEquals(true, loader.is(':mage-loader'));
};
// @TODO Need to be fixed to avoid errors on the bamboo server in context of MAGETWO-5085 ticket
/*LoaderTest.prototype._testCreateOnBeforeSend = function() {
    /*:DOC += <div id="loader"></div> */
/*  var loader = jQuery('#loader').trigger('ajaxSend');
    assertEquals(true, loader.is(':mage-loader'));
    loader.loader('destroy');
};*/
LoaderTest.prototype.testLoaderOnBody = function() {
    var loader = jQuery('body').loader();
    loader.loader('show');
    assertEquals(true, jQuery('body div:first').is('.loading-mask'));
    loader.loader('destroy');
};
LoaderTest.prototype.testLoaderOnDOMElement = function() {
    var loader = jQuery('#loader').loader(),
        loaderInstance = this.getInstance();
    loader.loader('show');
    assertEquals(true, loader.find(':first-child').is(loaderInstance.loader));
};
LoaderTest.prototype.testLoaderOptions = function() {
    /*:DOC += <div id="loader"></div> */
    var loader = jQuery('#loader').loader({
            icon: 'icon.gif',
            texts: {
                loaderText: 'Loader Text',
                imgAlt: 'Image Alt Text'
            }
        }),
        loaderInstance = this.getInstance();
    loader.loader('show');
    assertEquals('icon.gif', loaderInstance.loader.find('img').attr('src'));
    assertEquals('Image Alt Text', loaderInstance.loader.find('img').attr('alt'));
    assertEquals('Loader Text', loaderInstance.loader.find('p').text());
    loader.loader('destroy');
    loader.loader({
        template:'<div id="test-template"></div>'
    });
    loader.loader('show');
    loaderInstance = this.getInstance();
    assertEquals(true, loaderInstance.loader.is('#test-template'));
    loader.loader('destroy');
};
LoaderTest.prototype.testHideOnComplete = function() {
    /*:DOC += <div id="loader"></div> */
    var loader = jQuery('#loader').loader();
    loader.loader('show');
    loaderIsVisible = jQuery('.loading-mask').is(':visible');
    loader.trigger('processStop');
    assertEquals(false, jQuery('.loading-mask').is(':visible') === loaderIsVisible);
};
LoaderTest.prototype.testRender = function() {
    /*:DOC += <div id="loader" style="widht:200px; height:200px;"></div> */
    var loader = jQuery('#loader').loader();
    loader.loader('show');
    assertEquals(true, $('.loading-mask').is(':visible'));
};
LoaderTest.prototype.testShowHide = function() {
    /*:DOC += <div id="loader" style="widht:200px; height:200px;"></div> */
    var loader = jQuery('#loader').loader();
    loader.loader('show');
    assertEquals(true, $('.loading-mask').is(':visible'));
    loader.loader('hide');
    assertEquals(false, $('.loading-mask').is(':visible'));
};
LoaderTest.prototype.testDestroy = function() {
    /*:DOC += <div id="loader"></div> */
    var loader = jQuery('#loader').loader(),
        loaderExist = loader.is(':mage-loader');
    loader.loader('destroy');
    assertEquals(false, loader.is(':mage-loader') === loaderExist);
};
