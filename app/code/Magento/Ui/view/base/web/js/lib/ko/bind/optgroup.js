/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([ 'ko' ], function (ko) {
    'use strict';

    var captionPlaceholder = {};
    ko.bindingHandlers['optgroup'] = {
        'init': function (element, valueAccessor, allBindings) {
            if (ko.utils.tagNameLower(element) !== "select")
                throw new Error("options binding applies only to SELECT elements");

            // Remove all existing <option>s.
            while (element.length > 0) {
                element.remove(0);
            }

            // Ensures that the binding processor doesn't try to bind the options
            return { 'controlsDescendantBindings': true };
        },
        'update': function (element, valueAccessor, allBindings) {
            var selectWasPreviouslyEmpty = element.length == 0;
            var previousScrollTop = (!selectWasPreviouslyEmpty && element.multiple) ? element.scrollTop : null;
            var includeDestroyed = allBindings.get('optionsIncludeDestroyed');
            var arrayToDomNodeChildrenOptions = {};

            // filteredArray = captionValue + unwrappedArray + o(...)
            var captionValue;
            var unwrappedArray = ko.utils.unwrapObservable(valueAccessor());
            var filteredArray;

            var previousSelectedValues;
            var itemUpdate = false;
            var callback = setSelectionCallback;
            var nestedOptionsLevel = -1;


            if (element.multiple) {
                previousSelectedValues = ko.utils.arrayMap(selectedOptions(), ko.selectExtensions.readValue);
            } else {
                previousSelectedValues = element.selectedIndex >= 0 ? [ ko.selectExtensions.readValue(element.options[element.selectedIndex]) ] : [];
            }

            if (unwrappedArray) {
                if (typeof unwrappedArray.length == "undefined") // Coerce single value into array
                    unwrappedArray = [unwrappedArray];

                // Filter out any entries marked as destroyed
                filteredArray = ko.utils.arrayFilter(unwrappedArray, function (item) {
                    return includeDestroyed || item === undefined || item === null || !ko.utils.unwrapObservable(item['_destroy']);
                });

            } else {
                // If a falsy value is provided (e.g. null), we'll simply empty the select element
            }
            arrayToDomNodeChildrenOptions['beforeRemove'] =
                function (option) {
                    element.removeChild(option);
                };
            if (allBindings['has']('optionsAfterRender')) {
                callback = function (arrayEntry, newOptions) {
                    setSelectionCallback(arrayEntry, newOptions);
                    ko.dependencyDetection.ignore(allBindings.get('optionsAfterRender'), null, [newOptions[0], arrayEntry !== captionPlaceholder ? arrayEntry : undefined]);
                }
            }

            filteredArray = formatOptions(filteredArray);

            ko.utils.setDomNodeChildrenFromArrayMapping(element, filteredArray, optionNodeFromArray, arrayToDomNodeChildrenOptions, callback);

            ko.dependencyDetection.ignore(function () {
                if (allBindings.get('valueAllowUnset') && allBindings['has']('value')) {
                    // The model value is authoritative, so make sure its value is the one selected
                    ko.selectExtensions.writeValue(element, ko.utils.unwrapObservable(allBindings.get('value')), true /* allowUnset */);
                } else {
                    // Determine if the selection has changed as a result of updating the options list
                    var selectionChanged;
                    if (element.multiple) {
                        // For a multiple-select box, compare the new selection count to the previous one
                        // But if nothing was selected before, the selection can't have changed
                        selectionChanged = previousSelectedValues.length && selectedOptions().length < previousSelectedValues.length;
                    } else {
                        // For a single-select box, compare the current value to the previous value
                        // But if nothing was selected before or nothing is selected now, just look for a change in selection
                        selectionChanged = (previousSelectedValues.length && element.selectedIndex >= 0)
                            ? (ko.selectExtensions.readValue(element.options[element.selectedIndex]) !== previousSelectedValues[0])
                            : (previousSelectedValues.length || element.selectedIndex >= 0);
                    }

                    // Ensure consistency between model value and selected option.
                    // If the dropdown was changed so that selection is no longer the same,
                    // notify the value or selectedOptions binding.
                    if (selectionChanged) {
                        ko.utils.triggerEvent(element, "change");
                    }
                }
            });

            if (previousScrollTop && Math.abs(previousScrollTop - element.scrollTop) > 20)
                element.scrollTop = previousScrollTop;


            function selectedOptions() {
                return ko.utils.arrayFilter(element.options, function (node) {
                    return node.selected;
                });
            }

            function applyToObject(object, predicate, defaultValue) {
                var predicateType = typeof predicate;
                if (predicateType == "function")    // Given a function; run it against the data value
                    return predicate(object);
                else if (predicateType == "string") // Given a string; treat it as a property name on the data value
                    return object[predicate];
                else                                // Given no optionsText arg; use the data value itself
                    return defaultValue;
            }

            function optionNodeFromArray(arrayEntry, index, oldOptions) {
                var option;

                if (oldOptions.length) {
                    previousSelectedValues = oldOptions[0].selected ? [ ko.selectExtensions.readValue(oldOptions[0]) ] : [];
                    itemUpdate = true;
                }

                if (arrayEntry === captionPlaceholder) {
                    option = element.ownerDocument.createElement("option");

                    ko.utils.setTextContent(option, allBindings.get('optionsCaption'));
                    ko.selectExtensions.writeValue(option, undefined);
                } else if (!arrayEntry['value']) { // empty value === optgroup
                    option = element.ownerDocument.createElement("optgroup");
                    option.setAttribute('label', arrayEntry.label);
                } else {
                    option = element.ownerDocument.createElement("option");
                    ko.selectExtensions.writeValue(option, arrayEntry.value);
                    ko.utils.setTextContent(option, arrayEntry.label);
                }
                return [option];
            }

            function setSelectionCallback(arrayEntry, newOptions) {
                // IE6 doesn't like us to assign selection to OPTION nodes before they're added to the document.
                // That's why we first added them without selection. Now it's time to set the selection.
                if (previousSelectedValues.length) {
                    var isSelected = ko.utils.arrayIndexOf(previousSelectedValues, ko.selectExtensions.readValue(newOptions[0])) >= 0;
                    ko.utils.setOptionNodeSelectionState(newOptions[0], isSelected);

                    // If this option was changed from being selected during a single-item update, notify the change
                    if (itemUpdate && !isSelected)
                        ko.dependencyDetection.ignore(ko.utils.triggerEvent, null, [element, "change"]);
                }
            }

            function strPad(string, times) {
                return  (new Array(times + 1)).join(string);
            }

            function formatOptions(options) {
                var res = [];
                nestedOptionsLevel++;
                if(!nestedOptionsLevel) { // zero level
                    // If caption is included, add it to the array
                    if (allBindings['has']('optionsCaption')) {
                        captionValue = ko.utils.unwrapObservable(allBindings.get('optionsCaption'));
                        // If caption value is null or undefined, don't show a caption
                        if (captionValue !== null && captionValue !== undefined && captionValue !== false) {
                            res.push(captionPlaceholder);
                        }
                    }
                }
                ko.utils.arrayForEach(options, function (option, index) {
                    var label, value;

                    value = applyToObject(option, allBindings.get('optionsValue'), option);
                    value = ko.utils.unwrapObservable(value);
                    label = applyToObject(option, allBindings.get('optionsText'), value);
                    label = strPad('\u00a0\u00a0', nestedOptionsLevel) + label;

                    if (Array.isArray(value)) {
                        res.push({
                            label: label,
                            value: undefined
                        });

                        res = res.concat(formatOptions(value));
                    } else {

                        res.push({
                            label: label,
                            value: value
                        });
                    }
                });
                nestedOptionsLevel--;
                return res;
            }
        }
    };
    ko.bindingHandlers['selectedOptions']['after'].push('optgroup');
});