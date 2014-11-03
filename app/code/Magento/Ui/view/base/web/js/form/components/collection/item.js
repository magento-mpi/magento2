/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    '../tab',
    'underscore',
    'mage/utils'
], function (Tab, _, utils) {
    'use strict';

    var defaults = {
        template:           'ui/form/components/collection/item',
        displayArea:        'body',
        label:              '',
        separator:          ' ',
        storeAs:            'activeCollectionItem',
        previewTpl:         'ui/form/components/collection/preview'     
    };

    var previewConfig = {
        separator: ' ',
        prefix: ''
    };

    var __super__ = Tab.prototype;

    function parsePreview(data){
        var items;

        if (typeof data === 'string') {
            data = {
                items: data
            };
        }

        data.items = utils.stringToArray(data.items);

        return _.defaults(data, previewConfig);
    }

    return Tab.extend({
        initialize: function () {
            _.extend(this, defaults);

            _.bindAll(this, 'getPreview', 'buildPreview', 'hasPreview');

            __super__.initialize.apply(this, arguments);
        },

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.displayed = {};

            this.observe({
                    'noPreview': true,
                    'indexed':   {},
                    'body':      [],
                    'head':      []
                });

            return this;
        },

        initElement: function (elem) {
            var region  = elem.displayArea || this.displayArea,
                indexed = this.indexed(); 

            __super__.initElement.apply(this, arguments);
            
            this[region].push(elem);
            
            indexed[elem.index] = elem;

            this.indexed(indexed);
        },

        updateState: function(){
            var hasPreview = _.some(this.displayed, function(hasPreview){
                return !!hasPreview;
            });

            this.noPreview(!hasPreview);
        },

        formatPreviews: function(previews){
            return previews.map(parsePreview);
        },

        buildPreview: function(data){
            var preview = this.getPreview(data.items),
                prefix  = data.prefix;

            this.updateState();

            return prefix + preview.join(data.separator);
        },

        hasPreview: function(data){
            return !!this.getPreview(data.items).length;
        },

        getPreview: function(items){
            var elems       = this.indexed(),
                displayed   = this.displayed;

            items = items.map(function(index){
                var elem    = elems[index],
                    preview = elem && elem.delegate('getPreview');

                displayed[index] = !!preview;
                
                return preview;
            });

            return _.compact(items);
        }
    });
});