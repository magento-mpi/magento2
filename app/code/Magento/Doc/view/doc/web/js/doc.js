/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define(['jquery'], function(jQuery){
    'use strict';
    var currentContentArea;
    var dictionary = {};
    jQuery.each(jQuery('[data-role="dictionary"]'), function(id, element) {
        var json = JSON.parse(element.innerHTML);
        jQuery.extend(dictionary, json);
    });
    var pageItems = [];
    var requiredHtml = 'Element %s is required!';

    /**
     * Collect item editable content and send to backend for saving
     * @param {jQuery} item
     * @private
     */
    var saveElement = function (item) {
        var view = jQuery(item.find('*[data-role="doc-item-content"]')[0]);
        var source = jQuery(item.find('*[data-role="doc-item-content-src"]')[0]);
        var content = source.val(),
            type = source.attr('content-type'),
            module = source.attr('module'),
            outline = source.attr('outline'),
            name = source.attr('doc-name');
        jQuery.ajax({
            url: '/doc/index',
            method: 'POST',
            data: {
                action: 'save',
                content: content,
                type: type,
                module: module,
                outline: outline,
                name: name
            },
            success: function (response) {
                view.html(response);
                view.show();
                jQuery('#toolbar-save').removeClass('hasChanges').removeClass('hasActivated');
                if (item.editor) {
                    item.editor.currentView.hide();
                } else {
                    view.html(content);
                    jQuery(source).hide();
                    JUMLY.scan(view, {});
                }
                ensureIsNotEmpty(view);
                applyDictionary(view);
            }
        });
    };
    /**
     * Check if item content is empty
     * @param {jQuery} item
     * @private
     */
    var ensureIsNotEmpty = function(item) {
        var content = item.html().trim();
        if (!content && !item.attr('optional')) {
            item.html(requiredHtml.replace(/%s/g, item.attr('data-type')));
        }
    };
    /**
     * Initialize wysiwyg editor for contenteditable area
     * @param {HTMLElement} element
     * @private
     */
    var initEditor = function(element) {
        if (element.attr('readonly')) {
            return;
        }
        var content = jQuery(element.find('*[data-role="doc-item-content"]')[0]);
        var srcContent = jQuery(element.find('*[data-role="doc-item-content-src"]')[0]);
        currentContentArea = srcContent[0];
        content.hide();
        srcContent.show();
        srcContent.css('min-height', content.height() + 100);
        //srcContent.css('width', content.width() + 100);

        srcContent[0].addEventListener("keyup", function() {
            if (srcContent.data('orig')) {
                if (srcContent.data('orig') !== srcContent.val()) {
                    jQuery('#toolbar-save').addClass('hasChanges');
                } else {
                    jQuery('#toolbar-save').removeClass('hasChanges');
                }
            }
        });

        jQuery.each(jQuery('#toolbar').find('[data-role-command]'), function(id, button) {
            button.addEventListener('mousedown', executeAction);
        });
    };
    /**
     * Main event listener for toolbar actions
     * @param {Event} event
     * @private
     */
    var executeAction = function (event) {
        var button = event.srcElement || event.target;
        var action = button.getAttribute('data-role-command');
        if (actions[action]) {
            actions[action](button);
        }
        event.stopPropagation();
    };
    /**
     * Wrap selected content range with given tag element
     * @param {HTMLElement} element
     * @param {String} tag
     * @param {Range} range
     * @private
     */
    var applyToRange = function(element, tag, range) {
        var len = currentContentArea.value.length;
        var start = currentContentArea.selectionStart;
        var end = currentContentArea.selectionEnd;
        var sel = currentContentArea.value.substring(start, end);
        var replace = '<'+tag+'>' + sel + '</'+tag+'>';
        currentContentArea.value = currentContentArea.value.substring(0,start) + replace +
            currentContentArea.value.substring(end,len);
    };
    var actions = {
        /**
         * Wrap selected text with '<b>' tag element
         * @param {HTMLElement} element
         */
        bold: function(element) {
            var selection = window.getSelection();
            if (selection.rangeCount) {
                var range = selection.getRangeAt(0);
                applyToRange(element, 'b', range);
            }
        },
        /**
         * Wrap selected text with '<i>' tag element
         * @param {HTMLElement} element
         */
        italic: function(element) {
            var selection = window.getSelection();
            if (selection.rangeCount) {
                var range = selection.getRangeAt(0);
                applyToRange(element, 'i', range);
            }
        },
        /**
         * Mark selected item content as "approved by" signature
         * @param {HTMLElement} element
         */
        approve: function(element) {
            if (selected) {
                selected.removeClass('denoted');
            }
        },
        /**
         * Mark selected item content as "required to change"
         * @param {HTMLElement} element
         */
        denote: function(element) {
            if (selected) {
                selected.addClass('denoted');
            }
        },
        /**
         * Collect all editable items on the page and send all one-by-one to backend for saving
         * @param {HTMLElement} element
         */
        save: function(element) {
            jQuery.each(pageItems, function (id, item) {
                saveElement(item);
            });
        }
    };
    /**
     * Find references to dictionary articles and activate dictionary popups
     * @param {HTMLElement} element
     * @private
     */
    var applyDictionary = function(element) {
        jQuery.each(jQuery.find('a[rel="dictionary"]'), function (id, el) {
            var item = jQuery(el);
            var word = item.attr('href');
            if (dictionary.content[word]) {
                item.attr('title', dictionary.content[word].description)
                item.attr('href', dictionary.content[word].url)
            }
        });
    };

    var init = {
        /**
         * Prepare item of article type for displaying and editing
         * @param {jQuery} item
         */
        article: function(item) {
            var content = jQuery(item.find('*[data-role="doc-item-content"]')[0]);
            var srcContent = jQuery(item.find('*[data-role="doc-item-content-src"]')[0]);

            srcContent.data('orig', srcContent.val());
            content.on('click', function (event) {
                initEditor(item);
                event.stopPropagation();
            });
            ensureIsNotEmpty(content);
            applyDictionary(content);
            pageItems.push(item);
        },
        /**
         * Prepare item of diagram type for displaying and editing
         * @param {jQuery} item
         */
        diagram: function(item) {
            var content = jQuery(item.find('*[data-role="doc-item-content"]')[0]);
            var script = jQuery(item.find('script[type="text/jumly+sequence"]')[0]);
            var srcContent = jQuery(item.find('*[data-role="doc-item-content-src"]')[0]);
            srcContent.data('orig', srcContent.val());
            item.on('click', function (event) {
                initEditor(item);
                event.stopPropagation();
            });
            pageItems.push(item);
        },
        /**
         * Prepare item of api type for displaying and editing
         * @param {jQuery} item
         */
        api: function(item) {
            item.on('click', function (event) {
                event.stopPropagation();
            });
        },
        /**
         * Prepare item of example type for displaying and editing
         * @param {jQuery} item
         */
        example: function(item) {
            item.on('click', function (event) {
                event.stopPropagation();
            });
        },
        /**
         * Prepare item of reference-dir type for displaying and editing
         * @param {jQuery} item
         */
        'reference-dir': function(item) {
            item.on('click', function (event) {
                event.stopPropagation();
            });
        },
        /**
         * Prepare item of reference-file type for displaying and editing
         * @param {jQuery} item
         */
        'reference-file': function(item) {
            item.on('click', function (event) {
                event.stopPropagation();
            });
        },
        /**
         * Prepare item of reference-code type for displaying and editing
         * @param {jQuery} item
         */
        'reference-code': function(item) {
            item.on('click', function (event) {
                event.stopPropagation();
            });
        }
    };

    if (webkitSpeechRecognition) {
        var buffer, recognitionActive, recognition = new webkitSpeechRecognition();
        buffer = jQuery('#speech-buffer');
        buffer.on('change', function(event) {
            if (!buffer.val()) {
                buffer.hide();
            }
        });

        recognition.continuous = true;
        recognition.lang = 'en';
        recognition.onresult = function(event) {
            var final = '';
            for (var i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    final += event.results[i][0].transcript;
                }
            }
            buffer.button.removeClass('isActive');
            if (final) {
                buffer.val(final);
                buffer.show();
            }
        };
        document.getElementById('toolbar-speech').addEventListener('click', function (event) {
            var element = event.target || event.srcElement;
            if (recognitionActive) {
                recognition.stop();
                recognitionActive = false;
            } else {
                recognition.start();
                recognitionActive = true;
                buffer.button = jQuery(element);
                buffer.button.addClass('isActive');
            }
        });
    }

    /**
     * Component Constructor
     * @param {HTMLElement} el
     * @param {Object} config
     */
    return function(el, config) {
        var element = jQuery(el);
        jQuery.each(element.find('div[data-role="doc-item"]'), function (id, el) {
            var item = jQuery(el);
            var dataType = item.attr('data-type');
            if (init[dataType]) {
                init[dataType](item);
            }
        });
    };
});