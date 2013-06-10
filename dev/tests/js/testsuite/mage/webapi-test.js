/**
 * {license_notice}
 *
 * @category    mage.js
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
WebapiTest = TestCase('WebapiTest');

WebapiTest.prototype.testConstructorSuccess = function() {
    var successCallback = function(){};
    new $.mage.webapi('baseUrl', {'timeout': 100, 'success': successCallback});
}

WebapiTest.prototype.testConstructorSuccessEmptyArgs = function() {
    new $.mage.webapi('baseUrl');
}

WebapiTest.prototype.testConstructorInvalidOptions = function() {
    expectAsserts(1);
    try {
        new $.mage.webapi('baseUrl', {'timeout': 100, 'invalid': 0});
    } catch (e) {
        var expectedException = "No such option: invalid";
        assertEquals("Invalid exception was thrown.", expectedException, e);
    }
}

WebapiTest.prototype.testConstructorInvalidBaseUrl = function() {
    expectAsserts(1);
    try {
        var invalidBaseUrl = 1;
        new $.mage.webapi(invalidBaseUrl);
    } catch (e) {
        var expectedException = "String baseUrl parameter required";
        assertEquals("Invalid exception was thrown.", expectedException, e);
    }
}

WebapiTest.prototype.testCallInvalidMethod = function() {
    var webapi = new $.mage.webapi('baseUrl');
    try {
        webapi.call('resourceUri', 'INVALID_HTTP_METHOD');
    } catch (e) {
        var expectedException = "Method name is not valid: INVALID_HTTP_METHOD";
        assertEquals("Invalid exception was thrown.", expectedException, e);
    }
}

WebapiTest.prototype.testCallSuccessCallback = function() {
    // ensure that custom successCallback was executed
    expectAsserts(1);
    var successCallback = function(response) {
        assertObject("Response is expected to be an object", response);
    }
    var webapi = new $.mage.webapi('baseUrl', {'success': successCallback});
    $.ajax = function(settings) {
        settings.success({});
    };
    webapi.call('products', 'GET');
}

WebapiTest.prototype.testCallErrorCallback = function() {
    // ensure that custom successCallback was executed
    expectAsserts(1);
    var errorCallback = function(response) {
        assertObject("Response is expected to be an object", response);
    }
    var webapi = new $.mage.webapi('baseUrl', {'error': errorCallback});
    $.ajax = function(settings) {
        settings.error({});
    };
    webapi.call('products', 'GET');
}
