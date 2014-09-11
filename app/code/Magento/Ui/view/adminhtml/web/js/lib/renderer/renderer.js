<!--
/**
 * {license_notice}
 *
 * @category    storage
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
-->
define([
    '../loader',
    './overrides',
    'jquery',
    '_'
], function(loader, overrides, $, _) {
    'use strict';

    return {

        render: function (template) {
            var isRendered = $.Deferred(),
                extendersToLoad = [],
                parent,
                extenders,
                extendersHtml = '',
                resolve = isRendered.resolve.bind(isRendered);

            template  = template.split(' ');
            extenders = template.slice(1);
            parent    = template[0];

            if (extenders.length) {
                extendersToLoad = extenders.map(loadTemplate);

                waitFor(extendersToLoad).done(function () {

                    toArray(arguments).forEach(function (chunk) {
                        extendersHtml += chunk;
                    });

                    extendersHtml = '<div data-template-extend="' + parent+ '">' + extendersHtml + '</div>';

                    this._parse(extendersHtml).done(resolve);

                }.bind(this)); 
            } else {

                this._render(parent).done(resolve);

            }

            return isRendered.promise();
        },

        _render: function(template) {
            var isLoaded = loader.loadTemplate(template);

            return isLoaded.then(this._parse.bind(this));
        },

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

        _overridePartsOf: function(template) {
            return {
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

    function loadTemplate(template) {
        return loader.loadTemplate(template);
    }

    function extractTemplatePath(node) {
        return node.dataset.templateExtend;
    }

    function getExtendNodesFrom(container) {
        return toArray(container.querySelectorAll('[data-template-extend]'))
    }

    function isEmpty(object) {
        return !Object.keys(object).length;
    }

    function wrap(nodes, container) {
        nodes.forEach(function (node) {
            container.appendChild(node);
        });
    }

    function capitalizeFirstLetter(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function createActionSelectorFor(action) {
        return '[data-part-' + action + ']';
    }

    function createPartSelectorFor(part) {
        return '[data-part="' + part + '"]';
    }

    function toArray(arrayLikeObject) {
        return Array.prototype.slice.call(arrayLikeObject);
    }

    function waitFor(promises) {
        return $.when.apply(this, promises);
    }
});