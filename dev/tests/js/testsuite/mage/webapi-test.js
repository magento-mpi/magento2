/**
 * {license_notice}
 *
 * @category    mage.js
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
WebapiTest = TestCase('WebapiTest');

/**
 * Test call method with required data only
 */
WebapiTest.prototype.testCallRequiredArgumentsOnly = function() {
    this.API = $.mage.webapi();

    var productResourceUri = this.API.resource.uri.products;
    var httpMethod = this.API.resource.method.get;
    var productId = 2;
    var baseUri = this.API.resource.uri.base;
    var resourceVersion = 1;
    var productRequest = {
        "version": resourceVersion
    };
    // ensure that $.ajax() was executed
    expectAsserts(3);
    $.ajax = function(settings) {
        var expectedUri = baseUri + resourceVersion + productResourceUri + productId;
        assertEquals("URI for API call does not match with expected one.", expectedUri, settings.url);
        assertEquals("HTTP method for API call does not match with expected one.", httpMethod, settings.type);
        var expectedData = null;
        assertEquals("Data for API call does not match with expected one.", expectedData, settings.data);
    };
    this.API.call(productResourceUri, httpMethod, productId, productRequest);
};

WebapiTest.prototype.testCallEmptyRequestObject = function() {
    this.API = $.mage.webapi();
    /** Mock function that posts requests */
    $.ajax = function(settings) {
        fail("In case of empty request object no data sending must occur.");
    }
    var emptyRequestObject = {};
    this.API.call('', '', emptyRequestObject);
};
