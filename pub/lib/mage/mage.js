/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint eqnull:true browser:true jquery:true*/
/*global head:true */
(function($) {
    "use strict";
    $.widget('mage.mage', {
        resources: {},

        /**
         * Process all inits
         * @protected
         */
        _init: function() {
            var inits = this._toArray(this.element.data('mage-init'));
            inits.push.apply(inits, this._toArray(this.options.init));
            $.each(inits, $.proxy(function(i, init){
                this._initComponent(init);
            }, this));
        },

        /**
         * Convert passed argument into an array
         * @param {(undefined|Object|Array)} a
         * @return {Array}
         * @protected
         */
        _toArray: function(a) {
            return !a ? [] : !$.isArray(a) ? [a] : a;
        },

        /**
         * Run initialization of a component
         * @param {Object} init setting for a component in format
         *      {name: {string}[, options: {Object}][, args: {Array}][, resources: {Array}]}
         * @private
         */
        _initComponent: function(init){
            // there's nothing to do if name is undefined
            if (!init.name) {
                return;
            }
            init.options = init.options || {};
            init.resources = (init.resources || this.resources[init.name] || []).slice();

            // Through event-listener 3-rd party developer can modify options and list of resources
            $($.mage).trigger($.Event(init.name + 'init', {target: this.element}), init);

            var handler = $.proxy(function() {
                this[init.name](init.options);
            }, this.element);

            if (init.resources.length) {
                this._onload(init.resources, handler);
            } else {
                handler();
            }
        },

        /**
         * Initializate inits when all resources are loaded
         * @param {Array} resources
         * @param {Function} handler
         * @private
         */
        _onload: function(resources, handler) {
            var args = resources;
            args.push(handler);
            head.js.apply(head, args);
        }
    });

    /**
     * Handler of components declared through data attribute
     */
    $.mage.init = function(context) {
        $('[data-mage-init]', context || document).mage();
        return context;
    };
})(jQuery);




(function($) {

    var _syncQueue = [];
    var _asyncQueue = [];
    var _cssQueue = [];

    // Add add arr to queue make sure elements are unique
    function _addToQueue(files, queue) {
        for (var i = 0; i < files.length; i++) {
            if (typeof files[i] === 'string' && $.inArray(files[i], queue) === -1) {
                queue.push(files[i]);
            }
        }
    }

    function _unique(arr) {
        var uniqueArr = [];
        for (var i = arr.length; i--;) {
            var val = arr[i];
            if ($.inArray(val, uniqueArr) === -1) {
                uniqueArr.unshift(val);
            }
        }
        return uniqueArr;
    }

    function _asyncLoad() {
        var x, s, i;
        head.js.apply({}, _unique(_asyncQueue));
        x = document.getElementsByTagName('script')[0];
        for (i = 0; i < _cssQueue.length; i++) {
            s = document.createElement('link');
            s.type = 'text/css';
            s.rel = 'stylesheet';
            s.href = _cssQueue[i];
            x.parentNode.appendChild(s);
        }
    }

    function _loadScript() {
        if (_syncQueue.length === 0) {
            _asyncLoad();
            return;
        }
        _syncQueue = _unique(_syncQueue);
        _syncQueue.push(_asyncLoad);
        head.js.apply({}, _syncQueue);
    }

    $.extend(true, $.mage, {
        language: {
            cookieKey: 'language',
            en: 'en',
            code: 'en'
        },

        event: (function() {
            this.trigger = function (customEvent, data) {
                $(document).triggerHandler(customEvent.toString(), data);
            };
            this.observe = function (customEvent, func) {
                $(document).on(customEvent.toString(), func);
            };
            this.removeObserver = function (customEvent, func) {
                $(document).unbind(customEvent, func);
            };
            return this;
        }()),

        constant: {
            KEY_BACKSPACE: 8,
            KEY_TAB: 9,
            KEY_RETURN: 13,
            KEY_ESC: 27,
            KEY_LEFT: 37,
            KEY_UP: 38,
            KEY_RIGHT: 39,
            KEY_DOWN: 40,
            KEY_DELETE: 46,
            KEY_HOME: 36,
            KEY_END: 35,
            KEY_PAGEUP: 33,
            KEY_PAGEDOWN: 34
        },

        load: (function() {
            this.jsSync = function () {
                _addToQueue(arguments, _syncQueue);
                return _syncQueue.length;
            };
            this.js = function () {
                _addToQueue(arguments, _asyncQueue);
                return _asyncQueue.length;
            };
            this.css = function () {
                _addToQueue(arguments, _cssQueue);
                return _cssQueue.length;
            };
            this.language = function (lang, jsMapping) {
                var language = $.mage.language.code = lang;
                if (language != null && language !== $.mage.language.en) {
                    _addToQueue(jsMapping.localize, _syncQueue);
                }
                return _syncQueue.length;
            };
            return this;
        }())
    });

    $(window).on('load', _loadScript);

})(jQuery);
