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
    /**
     * Main namespace for Magento extansions
     * @type {Object}
     */
    $.mage = {};
})(jQuery);

/**
 * Plugin mage and group of heplers for it
 */
(function($) {
    "use strict";
    /**
     * Plugin mage, initialize components on elements
     * @param {string} name — component name
     * @param {}
     * @return {Object}
     */
    $.fn.mage = function() {
        var name = arguments[0],
            args = Array.prototype.slice.call(arguments, 1);
        return this.each(function(){
            var inits = _getInitData(this);
            if (name) {
                inits[name] = args;
            }
            $.each(inits, $.proxy(_initComponent, this));
        });
    };

    /**
     * Storage of declared resources
     * @type {Object}
     * @private
     */
    var _resources = {};

    /**
     * Execute initialization callback when all resources are loaded
     * @param {Array} args - list of resources
     * @param {Function} handler - initialization callback
     * @private
     */
    function _onload(args, handler) {
        args.push(handler);
        head.js.apply(head, args);
    }

    /**
     * Run initialization of a component
     * @param {Object} init - setting for a component in format
     *      {name: {string}[, options: {Object}][, args: {Array}][, resources: {Array}]}
     * @private
     */
    function _initComponent(name, args) {
        /*jshint validthis: true */
        var init = {
            name: name,
            args: $.makeArray(args),
            resources: (_resources[name] || []).slice()
        };
        // Through event-listener 3-rd party developer can modify options and list of resources
        $($.mage).trigger($.Event(name + 'init', {target: this}), init);
        // Component name was deleted, so there's nothing else to do
        if (!init.name) {
            return;
        }
        // Build an initialization handler
        var handler = $.proxy(function() {
            this[init.name].apply(this, init.args);
        }, $(this));
        if (init.resources.length) {
            _onload(init.resources, handler);
        } else {
            handler();
        }
    }

    /**
     * Define init-data from an element,
     *     if JSON is not well-formed then evaluate init-data by manually
     * @param {Element} elem
     * @return {Object}
     * @private
     */
    function _getInitData(elem) {
        /*jshint eval: true */
        var inits = $(elem).data('mage-init') || {};
        // in case it's not well-formed JSON inside data attribute, evaluate it manually
        if (typeof inits === 'string') {
            try {
                inits = eval('(' + inits + ')');
            } catch (e) {
                inits = {};
            }
        }
        return inits;
    }

    /**
     * Find all elements with data attribute and initialize them
     * @param {Element} elem - context 
     * @private
     */
    function _init(elem) {
        $('[data-mage-init]', elem).mage();
    }

    $.extend($.mage, {
        /**
         * Handle all components declared via data attribute
         * @return {Object} $.mage
         */
        init: function() {
            _init(document);
            /**
             * Init components inside of dynamically updated elements
             */
            $('body').on('contentUpdated', function(e){
                _init(e.target);
            });
            return this;
        },

        /**
         * Declare a new component based on already declared one in the mage widget
         * @param {string} component - name of a new component
         *      (can be the same as a name of super component)
         * @param {string} from - name of super component
         * @param {(undefined|Object|Array)} resources - list of resources
         * @return {Object} $.mage
         */
        extend: function(component, from, resources) {
            resources = $.merge(
                (_resources[from] || []).slice(),
                $.makeArray(resources)
            );
            this.component(component, resources);
            return this;
        },

        /**
         * Declare a new component or several components at a time in the mage widget
         * @param {(string|Object)} component - name of component
         *      or several componets with lists of required resources
         *      {component1: {Array}, component2: {Array}}
         * @param {(string|Array)} resources - URL of one resource or list of URLs
         * @return {Object} $.mage
         */
        component: function(component) {
            if ($.isPlainObject(component)) {
                $.extend(_resources, component);
            } else if (typeof component === 'string' && arguments[1]) {
                _resources[component] = $.makeArray(arguments[1]);
            }
            return this;
        },

        /**
         * Helper allows easily bind handler with component's initialisation
         * @param {string} component - name of a component
         *      which initialization shold be customized
         * @param {(string|Function)} selector [optional]- filter of component's elements
         *      or a handler function if selector is not defined
         * @param {Function} handler - handler function
         * @return {Object} $.mage
         */
        onInit: function(component, selector, handler) {
            if (!handler) {
                handler = selector;
                selector = null;
            }
            $(this).bind(component + 'init', function(e, init) {
                if (!selector || $(e.target).is(selector)) {
                    handler.apply(init, init.args || $.makeArray(init.options));
                }
            });
            return this;
        }
    });
})(jQuery);




/*(function($) {

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

})(jQuery);*/
