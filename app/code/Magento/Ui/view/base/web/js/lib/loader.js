/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'jquery'
], function(_, $) {
    'use strict';
    
    var storage = window.localStorage;

    /**
     * Formats path of type "path.to.template" to RequireJS compatible
     * @param  {String} path
     * @return {String} - formatted template path
     */
    function formatTemplatePath(path) {
        return 'text!' + path.replace(/^([^\/]+)/g, '$1/template') + '.html';
    }

    /**
     * Waits for all items in passed array of promises to resolve.
     * @param  {Array} promises - array of promises
     * @return {Deferred} - promise of promises to resolve
     */
    function waitFor(promises) {
        return $.when.apply(this, promises);
    }

    function load(path, promise){
        require([path], function (template) {
            storage.setItem(path, template);

            if(promise){
                promise.resolve(template);
            }
        });
    }

    return {
        /**
         * Loops over arguments and loads template for each.
         * @return {Deferred} - promise of templates to be loaded
         */
        loadTemplate: function() {
            var isLoaded    = $.Deferred(),
                templates   = _.toArray(arguments),
                timeout     = templates.length > 1;

            waitFor(templates.map(this._loadTemplate)).done(function () {
                templates = _.toArray(arguments);

                if(timeout){
                    setTimeout(function(){
                        isLoaded.resolve.apply(isLoaded, templates);
                    }, 0);
                }
                else{
                    isLoaded.resolve.apply(isLoaded, templates);
                }
                
            });

            return isLoaded.promise();
        },

        _loadTemplate: function (name) {
            var isLoaded    = $.Deferred(),
                path        = formatTemplatePath(name),
                cached      = storage.getItem(path);

            if(cached){
                isLoaded.resolve(cached);            
                load(path);
            }
            else{
                load(path, isLoaded);
            }

            return isLoaded.promise();
        }
    }
});