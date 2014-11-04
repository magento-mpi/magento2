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

            this.observe({
                'noPreview': true,
                'body':      [],
                'head':      []
            });

            return this;
        },

        initProperties: function () {
            __super__.initProperties.apply(this, arguments);

            this.displayed  = {};
            this.indexed    = {};

            return this;
        },

        initElement: function (elem) {
            __super__.initElement.apply(this, arguments);

            this.insertToArea(elem)
                .insertToIndexed(elem);
        },

        insertToArea: function (elem) {
            var regions = [];

            elem.displayArea = elem.displayArea || this.displayArea;

            regions = this.elems.groupBy('displayArea');

            _.each(regions, function (elems, region) {
                this[region](elems);
            }, this);

            return this;
        },

        insertToIndexed: function (elem) {
            this.indexed[elem.index] = elem;

            return this;
        },

        formatPreviews: function(previews){
            return previews.map(parsePreview);
        },

        buildPreview: function(data){
            var preview = this.getPreview(data.items),
                prefix  = data.prefix;

            this.updatePreview();

            return prefix + preview.join(data.separator);
        },

        hasPreview: function(data){
            return !!this.getPreview(data.items).length;
        },

        getPreview: function(items){
            var elems       = this.indexed,
                displayed   = this.displayed;

            items = items.map(function(index){
                var elem    = elems[index],
                    preview = elem && elem.delegate('getPreview');

                displayed[index] = !!preview;
                
                return preview;
            });

            return _.compact(items);
        },

        updatePreview: function(){
            this.noPreview(!_.some(this.displayed));
        }
    });
});