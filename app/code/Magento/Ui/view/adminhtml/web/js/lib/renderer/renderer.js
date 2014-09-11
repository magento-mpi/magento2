/**
 * {license_notice}
 *
 * @category    storage
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '../loader',
    './overrides',
    'jquery',
    '_'
], function(loader, overrides, $, _) {
    'use strict';

    return {

        /**
         * Renders template and it's extenders using this._parse function.
         * Loads all extenders then merges them and wraps into div[data-template-extend="parent"] where parent is target template.
         * If no extenders provider, simply loads target template and passes execution to _parse.
         * @param  {String} template - string, representing path to core template and it's extenders.
         * @return {Deferred} - Promise of template to be rendered. Is being resolved with array of HTML elements.
         */
        render: function (template) {
            var isRendered = $.Deferred(),
                extendersToLoad = [],
                parent,
                extenders,
                extendersHtml = '',
                resolve = isRendered.resolve.bind(isRendered),
                loadTemplate = this._load.bind(this),
                parseTemplate = this._parse.bind(this);

            if (typeof template === 'string') {
                parent = template    
            } else {
                extenders = template.extenders;
                parent    = template.name;    
            }

            if (extenders.length) {
                extendersToLoad = extenders.map(function (extender) {
                    return loadTemplate(extender);
                });

                waitFor(extendersToLoad).done(function () {

                    toArray(arguments).forEach(function (chunk) {
                        extendersHtml += chunk;
                    });

                    extendersHtml = '<div data-template-extend="' + parent+ '">' + extendersHtml + '</div>';

                    parseTemplate(extendersHtml).done(resolve);

                }); 
            } else {

                loadTemplate(parent)
                    .then(parseTemplate)
                    .done(resolve);
            }

            return isRendered.promise();
        },

        /**
         * Loads templates via loader module.
         * @return {Deferred} - Promise of templates to be loaded
         */
        _load: function () {
            return loader.loadTemplate.apply(loader, arguments);
        },

        /**
         * Takes raw text (html), parses it, puts it to docuemntFragment container.
         * Looks up for all [data-template-exted] attributes, creates array of extend nodes.
         * Maps this array to extractTemplatePath to have all extend points pathes gathered.
         * Maps pathes to this.render method (which returns promise) and waits for this array to resolve.
         * Then looks up for [data-part-*] attributes and creates map of new parts.
         * Then overrides parent template's corresponding parts with new parts.
         * @param  {String} rawHtml - loaded raw text (html)
         * @return {Deferred} - Promise of template to be parsed. Is being resolved with array of HTML elements.
         */
        _parse: function(rawHtml) {
            var templatePath,
                templateContainer,
                extendNodes,
                templatesToRender = [],
                extendPointsToRender = [];

            templateContainer = document.createDocumentFragment();

            wrap($.parseHTML(rawHtml), templateContainer);

            extendNodes          = getExtendNodesFrom(templateContainer);
            templatesToRender    = extendNodes.map(extractTemplatePath, this)
            extendPointsToRender = templatesToRender.map(this.render, this);

            return waitFor(extendPointsToRender).then(function() {
                var correspondingExtendNode,
                    container,
                    newParts = [],
                    args = toArray(arguments);

                args.forEach(function(renderedNodes, idx) {
                    container = document.createDocumentFragment();
                    wrap(renderedNodes, container);

                    correspondingExtendNode = extendNodes[idx];
                    newParts = this._buildPartsMapFrom(correspondingExtendNode);

                    $(correspondingExtendNode).empty();

                    this._overridePartsOf(container)
                        .by(newParts)
                        .appendTo(correspondingExtendNode);

                }, this);

                return toArray(templateContainer.children);
            }.bind(this));
        },

        /**
         * Builds parst map from HTML element by looking for all available override actions selectors.
         * @param  {HTMLElement} container - container to look up for new parts declarations
         * @return {Object} - Map of parts to apply. E.g. { toolbar: { replace: [HTMLElement1, HTMLElement2], append: [HTMLElement3] } }
         */
        _buildPartsMapFrom: function(container) {
            var partsMap = {},
                actionNodes,
                partSelector,
                targetPart,
                actions = overrides.getActions();

            actions.forEach(function(action) {
                partSelector = createActionSelectorFor(action);
                actionNodes  = toArray(container.querySelectorAll(partSelector));

                actionNodes.forEach(function(node) {
                    targetPart = node.dataset['part' + capitalizeFirstLetter(action)];

                    if (!partsMap[targetPart]) {
                        partsMap[targetPart] = {};
                    }

                    targetPart = partsMap[targetPart];

                    if (!targetPart[action]) {
                        targetPart[action] = [];
                    }

                    targetPart[action].push(node);
                });
            });

            return partsMap;
        },

        /**
         * Caches template and returns object for the sake of chaining
         * @param  {HTMLElement} template - container to look for parts to be overrided by new ones.
         * @return {Object}
         */
        _overridePartsOf: function(template) {
            return {

                /**
                 * Loops over newParts map and invokes override actions for each found.
                 * @param  {Object} newParts - the result of _buildPartsMapFrom method.
                 * @return {Object} - Returns object for the sake of chaining
                 */
                by: function(newParts) {
                    var oldElement;

                    _.each(newParts, function(actions, partName) {
                        _.each(actions, function(newElements, action) {

                            oldElement = template.querySelector(createPartSelectorFor(partName));
                            overrides[action](
                                oldElement,
                                newElements
                            );

                        });
                    });

                    return {

                        /**
                         * Appends template's (overrided already) children to extendNode.
                         * @param  {HTMLElement} extendNode - initial container of new parts declarations
                         */
                        appendTo: function(extendNode) {
                            if (template.hasChildNodes()) {
                                toArray(template.children).forEach(function (child) {
                                    extendNode.appendChild(child);
                                });
                            }
                        }
                    }
                }
            }
        }
    };

    /**
     * Extracts template path from node by [data-part-extend] attribute
     * @param  {HTMLElement} node - node to look up for [data-part-extend] attr
     * @return {String} - value of [data-part-extend] attribute
     */
    function extractTemplatePath(node) {
        return node.dataset.templateExtend;
    }

    /**
     * Looks up for [data-template-extend] selector in container.
     * @param  {HTMLElement} container - node to lookup
     * @return {Array} - array of found HTML elements
     */
    function getExtendNodesFrom(container) {
        return toArray(container.querySelectorAll('[data-template-extend]'))
    }

    /**
     * Checks if passed object has keys.
     * @param  {Object}  object - target object
     * @return {Boolean} - true, if object has no keys
     */
    function isEmpty(object) {
        return !Object.keys(object).length;
    }

    /**
     * Wraps nodes into container
     * @param  {Array} nodes - array of nodes
     * @param  {HTMLElement} container - target container
     */
    function wrap(nodes, container) {
        nodes.forEach(function (node) {
            container.appendChild(node);
        });
    }

    /**
     * Capitalizes first letter of passed string and returns new string.
     * @param  {String} str - string to format
     * @return {String} - result string
     */
    function capitalizeFirstLetter(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    /**
     * Creates action selector.
     * @param  {String} action
     * @return {String} - Action selector
     */
    function createActionSelectorFor(action) {
        return '[data-part-' + action + ']';
    }

    /**
     * Creates data-part selector.
     * @param  {String} part
     * @return {String} - Part selector
     */
    function createPartSelectorFor(part) {
        return '[data-part="' + part + '"]';
    }

    /**
     * Converts arrayLikeObject to array
     * @param  {Object|Array} arrayLikeObject - target
     * @return {Array} - result array
     */
    function toArray(arrayLikeObject) {
        return Array.prototype.slice.call(arrayLikeObject);
    }

    /**
     * Waits for all items in passed array of promises to resolve.
     * @param  {Array} promises - array of promises
     * @return {Deferred} - promise of promises to resolve
     */
    function waitFor(promises) {
        return $.when.apply(this, promises);
    }
});