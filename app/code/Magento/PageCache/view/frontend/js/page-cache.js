/**
 * Handles additional ajax request for rendering user private content
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true expr:true*/
(function ($) {
    "use strict";
    $.widget('mage.pageCache', {
        options: {
            url: '/',
            startBlockPattern: /^ BLOCK (.+) $/,
            endBlockPattern: /^ \/BLOCK (.+) $/,
            versionCookieName: 'private_content_version',
            handles: []
        },
        _create: function () {
            var version = $.mage.cookies.get(this.options.versionCookieName) || '';
            if (!version) {
//                return ;
            }
            var blocks = this._searchBlocks(this.element.comments());
            this._ajax(blocks, version);
        },
        _searchBlocks: function (elements) {
            var blocks = [],
                tmp = {};
            for (var i = 0; i < elements.length; i++) {
                var el = elements[i],
                    matches = this.options.startBlockPattern.exec(el.nodeValue),
                    blockName = null;

                if (matches) {
                    blockName = matches[1];
                    tmp[blockName] = {
                        name: blockName,
                        startElement: el
                    };
                } else {
                    matches = this.options.endBlockPattern.exec(el.nodeValue);
                    if (matches) {
                        blockName = matches[1];
                        if (tmp[blockName]) {
                            tmp[blockName].endElement = el;
                            blocks.push(tmp[blockName]);
                            delete tmp[blockName];
                        }
                    }
                }
            }
            return blocks;
        },
        _ajax: function (blocks, version) {
            var data = {
                blocks: [],
                handles: this.options.handles,
                version: version
            };
            for (var i = 0; i < blocks.length; i++) {
                data.blocks.push(blocks[i].name);
            }
            if (!data) {
                return;
            }
            $.ajax({
                url: this.options.url,
                data: data,
                type: 'GET',
                cache: true,
                dataType: 'json',
                context: this,
                success: function (response) {
                    for(var blockName in response) {
                        if (!response.hasOwnProperty(blockName)) {
                            continue;
                        }
                        for (var i = 0; i < blocks.length; i++) {
                            var block = blocks[i];
                            if (block.name == blockName) {
                                var end = $(block.endElement).next();
                                $(block.startElement).nextUntil(end).remove();
                                $(end).before(response[blockName]);
                            }
                        }
                    }
                }
            });
        }
    });
})(jQuery);