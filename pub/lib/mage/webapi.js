/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    "use strict";
    $.extend(true, $, {
        mage: {
            /**
             * Webapi object constructor
             *
             * @param {Object|undefined} args Arguments for constructor, see "options" variable
             * @returns {{method: Object, responseType: Object, call: Function, Product: Function}}
             */
            webapi: function(args) {
                var resource = {
                    uri: {
                        // @todo how do we determine base_url?
                        base:     '/webapi/rest',
                        products: '/products/'
                    }
                };

                var responseType = {
                    json: 'application/json',
                    xml:  'application/xml'
                };

                var validResponseTypes = [responseType.json, responseType.xml];

                var method = {
                    create: 'POST',
                    update: 'PUT',
                    get:    'GET',
                    delete: 'DELETE'
                };

                var validMethods = [method.create, method.update, method.get, method.delete];

                var options = {
                    timeout: 5000,
                    responseType: responseType.json,
                    success: null,
                    error: null
                };

                // Check whether passed options comply with what we allow
                if (typeof args === 'object') {
                    for (var option in args) {
                        if (args.hasOwnProperty(option) && options.hasOwnProperty(option)) {
                            options[option] = args[option];
                        } else {
                            throw 'No such option: ' + option;
                        }
                    }
                }

                /**
                 * Public interface
                 *
                 * @type {{method: Object, responseType: Object, call: Function, Product: Function}}
                 */
                var Webapi = {
                    method: method,
                    responseType: responseType,

                    /**
                     * Makes an API request
                     *
                     * @param {string}           resUri  Resource URI request to be sent to, e.g. '/v1/products/'
                     * @param {string}           method  Request method, e.g. GET, POST, etc.
                     * @param {*}                data    Payload to be sent to the server
                     * @param {string|undefined} version Optional: version API version, e.g. 'v1' (if not specified
                     *                                   using URI)
                     * @returns {jqXHR}
                     */
                    call: function(resUri, method, data, version) {
                        /**
                         * Helper function to validate request method
                         *
                         * @param {string} method
                         * @returns {string}
                         */
                        var validateMethod = function(method) {
                            if (validMethods.indexOf(method) == -1) {
                                throw 'Method name is not valid: ' + method;
                            }

                            return method;
                        };

                        var that = this;

                        /**
                         * Helper function to construct URIs
                         *
                         * @param {string} resUri
                         * @param {string} method
                         * @param {*}      data
                         * @param {string} version
                         * @returns {string}
                         */
                        var getUrl = function(resUri, method, data, version) {
                            var ensureForwardSlash = function (uri) {
                                return uri[0] == '/' ? uri : '/' + uri;
                            };

                            if (version) {
                                resUri = version + ensureForwardSlash(resUri);
                            }

                            if (data && [that.method.get, that.method.delete].indexOf(method) != -1) {
                                // Append data for GET and DELETE request methods as it's simple ID (usually int)
                                resUri += data;
                            }

                            resUri = resource.uri.base + ensureForwardSlash(resUri);

                            return resUri;
                        };

                        /**
                         * Helper function to get Accept header value
                         *
                         * @returns {string}
                         */
                        var getAcceptHeaderValue = function() {
                            if (validResponseTypes.indexOf(options.responseType) == -1) {
                                throw 'Response type is not valid: ' + options.responseType;
                            }

                            return options.responseType;
                        };

                        return $.ajax({
                            url: getUrl(resUri, method, data, version),
                            type: validateMethod(method),
                            data: data,
                            dataType: 'text',
                            timeout: options.timeout,
                            processData: false, // Otherwise jQuery will try to append 'data' to query URL

                            beforeSend: function (request) {
                                request.setRequestHeader('Accept', getAcceptHeaderValue());
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
                    },

                    /**
                     * Syntax sugar over call(). Example usage: $.mage.webapi.Product('v1').get({...})
                     *
                     * @param {string} version
                     * @returns {{get: Function, create: Function}}
                     */
                    Product: function(version) {
                        if (!(typeof version === 'string' && /v\d/.test(version))) {
                            throw 'Incorrect version format: ' + version;
                        }

                        return {
                            get: function(data) {
                                return Webapi.call(resource.uri.products, method.get, data, version);
                            },

                            create: function(data) {
                                return Webapi.call(resource.uri.products, method.create, data, version);
                            }
                        }
                    }
                };

                return Webapi;
            }
        }
    });
})(jQuery);
