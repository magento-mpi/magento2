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
        storeAs:            'activeCollectionItem'
    };

    var __super__ = Tab.prototype;

    return Tab.extend({
        initialize: function () {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        },

        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.labelParts     = this.labelParts || [];
            this.previewParts   = this.previewParts || [];

            this.observe({
                    'labels':   [],
                    'previews': [],
                    'body':     [],
                    'head':     []
                });

            return this;
        },

        initElement: function (elem) {
            var region = elem.displayArea || this.displayArea;

            __super__.initElement.apply(this, arguments);
            
            this[region].push(elem);
            
            this.insertTo('previews',   this.previewParts,    elem)
                .insertTo('labels',     this.labelParts,      elem);
        },

        insertTo: function(container, map, elem){
            var items   = this[container](),
                index   = map.indexOf(elem.index);

            if(~index){
                items.splice(index, 0, elem);
                
                this[container](_.compact(items));
            }

            return this;
        },

        getLabel: function(){
            var label;

            label = this.labels().map(this.getPreivew);
            label = label.join(this.separator).trim();

            return label || this.label;
        },

        getPreivew: function(elem){
            return elem.delegate('getPreview');
        }
    });
});