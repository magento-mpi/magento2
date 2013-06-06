/**
 * {license_notice}
 *
 * @category    webapi
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    "use strict";

    /**
     * This js client gives developers seamless access to API Services
     * by abstracting api calling details
     */
    $.widget('mage.webapi', {

        // API instance properties
        resource: {
            //Available resource URIs
            uri: {
                base: '/webapi/rest/',
                products: '/catalogProducts/',
                customers: '/customers/'
            },

            //Allowed HTTP actions
            method: {
                //list: 'GET', //Will be replaced by search/filter
                create: 'POST',
                update: 'PUT',
                get: 'GET',
                delete: 'DELETE'
            }
        },

        responseType: {
            json: 'application/json',
            xml: 'application/xml'
        },

        /**
         * options with default values for setting up the API widget
         */
        options: {
            timeout: 3000, // TODO:Default 3s, needs to be verified
            token: null, // may be deprecated
            responseType: 'application/json' // response type set by user. Defaulted to json
        },

        /**
         * Initialized when the webapi instance is created
         * @private
         */
        _create: function() {
            // TODO: Add tasks on instance creation. eg clean up cache
        },

        /**
         * Can be used to track access to the widget instance
         * @private
         */
        _init: function() {
        },

        /**
         * Helper function to construct URIs
         * This is a very simple version of constructing URIs. It will evolve for complex Service access
         * like search/filtering
         * @private
         * @param resourceURI
         * @param method
         * @param version
         * @param data
         * @return {String}
         */
        _getURL: function(resourceURI, method, version, data) {
            var uri = version ? version + resourceURI : resourceURI;
            if (data && method !== this.resource.method.create && method !== this.resource.method.update) {
                uri += data;
            }
            return this.resource.uri.base + uri;
        },

        /**
         * Helper function to get Accept header value
         * @private
         * @param headerObj
         * @return {Object} - returning accept header value
         */
        _getAcceptHeaderValue: function() {
            return this.options.responseType in this.responseType ? this.responseType[this.options.responseType] : this.responseType.json;
        },

        /**
         * TODO / TBD : May not be required. Web clients may purely depend on cookie based authentication
         * @private
         * @param headerObj
         */
        _setAuthHeaders: function(headerObj) {

        },

        /**
         * Generic public api to request resources from the Magento Web Services
         * @public
         * @param {String} resURI - Should be one of $.mage.webapi.resource.uri
         * @param {String} method - Should be one of $.mage.webapi.resource.method
         * @param {String|Object} data - optional - Object or String depending on api operation
         * @param {Object} reqObj - optional - Below is the object and allowed properties
         *                              {
         "version":1, //version of the api
         "successCallback": successCallBackFunction,
         "errorCallBack": errorCallBackFunction
         };
         */
        call: function(resURI, method, data, reqObj) {
            if (!reqObj) {
                return;
            }

            var url = this._getURL(resURI, method, reqObj.version, data);

            //No data necessary if its part of the uri
            data = url.indexOf(data) > -1 ? null : data;

            this._poster(url, method, data, reqObj.successCallback, reqObj.errorCallBack);
        },

        /**
         * Utility for processing and sending webapi resource request
         * @private
         * @param url
         * @param method
         * @param data
         * @param successCallback
         * @param errorCallback
         * @private
         */
        _poster: function(url, method, data, successCallback, errorCallBack) {
            $.ajax({
                url: url,
                type: method, //HTTP Method
                data: data,
                dataType: "text", // Setting this as text to give a plaintext response back. The client can process it as per his needs
                timeout: this.options.timeout,
                context: this,
                beforeSend: function(request) {
                    request.setRequestHeader('Accept', this._getAcceptHeaderValue());
                    this.element.trigger("webapi.beforeSend");
                },
                success: function(response) {
                    if (response) {
                        this.element.trigger("webapi.success");
                        if (successCallback) {
                            successCallback.call(this, response);
                        }
                    }
                },

                error: function(xhr, error) {
                    this.element.trigger("webapi.error");
                    if (errorCallBack) {
                        errorCallBack.call(this,xhr, error);
                    }
                },

                complete: function() {
                    this.element.trigger("webapi.complete");
                }
            });
        }

    });

    /**
     * Extending the core api widget to provide resource friendly access functions
     */
    $.widget('mage.webapi', $.mage.webapi, {

        _create: function() {
            this._super();
            // Set the context of the widget instantiated by the caller.
            this.Product.context = this;
        },

        /**
         * Providing the <Service>.operationName() APIs within a widget paradigm
         */
        Product: {

            context: undefined,

            resource: $.mage.webapi.prototype.resource,

            callApi: $.mage.webapi.prototype.call,

            /**
             * API to access
             * @public
             * @param {String|Object} data - optional - Object or String depending on Service operation
             * @param {Object} reqObj - optional - Below is the object and allowed properties
             *                              {
             "version":1, //version of the api
             "successCallback": successCallBackFunction,
             "errorCallBack": errorCallBackFunction
             };
             */
            get: function(data, reqObj) {
                if (this.context) {
                    this.callApi.call(this.context, this.resource.uri.products, this.resource.method.get, data, reqObj);
                }
            },

            create: function(data, reqObj) {
                if (this.context) {
                    this.callApi.call(this.context, this.resource.uri.products, this._resource().method.create, data, reqObj);
                }
            },

            update: function() {
                //TO implement
            }
        },

        Customer: {

            create: function(reqObj) {
                this.call(this._resource().uri.customers, this._resource().method.get, 1, reqObj);
            },

            update: function() {
                //TO implement
            }
        }

    });

    /**
     *
     * Below is an example on how to instantiate the js API widget to invoke Webapi services
     *
     */

    var successCallBack = function(response) {
        console.log(response);
    };

    var errorCallBack = function(xhr, error) {
        console.log("Error! Response:" + xhr.responseText);
    };

    var productRequest = {
        "version": 1,
        "successCallback": successCallBack,
        "errorCallBack": errorCallBack
    };

    /**
     * Instantiate the webapi
     */
    var API = $.mage.webapi({
        timeout: 5000
        //responseType: 'xml' //default is json
    });

    /**
     * Access using the resource specific API
     * Usage : apiInstance.<Service>.<operation>( [data],[requestOptions] )
     */
    API.Product.get(1, productRequest);

    //OR

    /**
     * Access using the generic API function
     * Usage : apiInstance.call('resource', 'operation', [data], [requestOptions])
     */
    API.call(API.resource.uri.products, API.resource.method.get, 1, productRequest);

})(jQuery);
