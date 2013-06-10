/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/* jshint jquery: true */
(function($){
    "use strict";

    /**
     * Webapi object constructor
     *
     * @param {string}           baseUrl Base URL
     * @param {Object|undefined} args    Arguments for constructor, see "options" variable
     * @returns {{method: Object, call: Function}}
     */
    $.mage.webapi = function(baseUrl, args) {
        /**
         * Resource-related parameters. Further extended by other domain objects like Product, etc.
         *
         * @const
         * @type {{uri: {base: string}}}
         */
        this.resource = {
            uri: {
                base: '' // Initialized below
            }
        };

        /**
         *
         *
         * @const
         * @type {{create: string, update: string, get: string, delete: string}}
         */
        this.method = {
            'create': 'POST',
            'update': 'PUT',
            'get': 'GET',
            'delete': 'DELETE'
        };

        var validMethods = [this.method.create, this.method.update, this.method.get, this.method['delete']];

        var options = {
            /**
             * Timeout for AJAX request
             */
            timeout: 5000,
            /**
             * Success AJAX call function handler
             */
            success: null,
            /**
             * Failed AJAX call function handler
             */
            error: null
        };

        // Check whether passed options comply with what we allow
        if (args && typeof args === 'object') {
            for (var option in args) {
                if (args.hasOwnProperty(option) && options.hasOwnProperty(option)) {
                    options[option] = args[option];
                } else {
                    throw 'No such option: ' + option;
                }
            }
        }

        if (!(baseUrl && typeof baseUrl === 'string')) {
            throw 'String baseUrl parameter required';
        }

        this.resource.uri.base = baseUrl;

        /**
         * Makes an API request
         *
         * @param {string}           resourceUri Resource URI request to be sent to, e.g. '/v1/products/'
         * @param {string}           method      Request method, e.g. GET, POST, etc.
         * @param {*}                data        Payload to be sent to the server
         * @param {string|undefined} version     Optional: API version, e.g. 'v1' (if not specified
         *                                       using URI)
         * @returns {jqXHR}
         */
        this.call = function(resourceUri, method, data, version) {
            /**
             * Helper function to validate request method
             *
             * @param {string} method
             * @returns {string}
             */
            function validateMethod(method) {
                if (validMethods.indexOf(method) === -1) {
                    throw 'Method name is not valid: ' + method;
                }

                return method;
            }

            var that = this;

            /**
             * Helper function to construct URIs
             *
             * @param {string}           resourceUri Resource URI request to be sent to, e.g. '/v1/products/'
             * @param {string}           method      Request method, e.g. GET, POST, etc.
             * @param {*}                data        Payload to be sent to the server
             * @param {string|undefined} version     Optional: API version, e.g. 'v1'
             *
             * @returns {string}
             */
            function getUrl(resourceUri, method, data, version) {
                function ensureForwardSlash(str) {
                    return str[0] === '/' ? str : '/' + str;
                }

                var resourceUrl = '';

                if (version) {
                    resourceUrl = version + ensureForwardSlash(resourceUri);
                }

                if (data && [that.method.get, that.method['delete']].indexOf(method) !== -1) {
                    // Append data for GET and DELETE request methods as it's simple ID (usually int)
                    resourceUrl += data;
                }

                resourceUrl = that.resource.uri.base + ensureForwardSlash(resourceUrl);

                return resourceUrl;
            }

            return $.ajax({
                url: getUrl(resourceUri, method, data, version),
                type: validateMethod(method),
                data: data,
                dataType: 'text',
                timeout: options.timeout,
                processData: false, // Otherwise jQuery will try to append 'data' to query URL
                cache: false, // Disable browser cache for GET requests

                beforeSend: function (request) {
                    request.setRequestHeader('Accept', 'application/json');
                },

                success: function (response) {
                    if (response) {
                        if (typeof options.success === 'function') {
                            options.success(response);
                        }
                    }
                },

                error: function (xhr, error) {
                    if (typeof options.error === 'function') {
                        options.error(xhr, error);
                    }
                }
            });
        };

        return this;
    };

    $.mage.webapi.prototype.constructor = $.mage.webapi;

    /**
     * Syntax sugar over call(). Example usage: $.mage.webapi.Product('v1').get({...})
     *
     * @param {string} version API version (e.g. 'v1')
     * @returns {{get: Function, create: Function}}
     */
    $.mage.webapi.prototype.Product = function(version) {
        if (!(typeof version === 'string' && /v\d+/.test(version))) {
            throw 'Incorrect version format: ' + version;
        }

        var that = this; // Points to $.mage.webapi
        that.resource.uri.products = '/products/';

        return {
            /**
             * Retrieves information about specific product
             *
             * @param productId Product ID
             * @returns {jqXHR}
             */
            get: function(productId) {
                return that.call(that.resource.uri.products, that.method.get, productId, version);
            },

            /**
             * Create a new product
             *
             * @param productData Example product data:
             *                    productData = {
             *                        "type_id": "simple",
             *                        "attribute_set_id": 4,
             *                        "sku": "1234567890",
             *                        "weight": 1,
             *                        "status": 1,
             *                        "visibility": 4,
             *                        "name": "Simple Product",
             *                        "description": "Simple Description",
             *                        "short_description": "Simple Short Description",
             *                        "price": 99.95,
             *                        "tax_class_id": 0
             *                    };
             * @returns {jqXHR}
             */
            create: function(productData) {
                return that.call(that.resource.uri.products, that.method.create, productData, version);
            }
        };
    };
})(jQuery);
