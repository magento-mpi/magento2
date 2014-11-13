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

    /**
     * Parses incoming data and returnes result merged with default preview config
     * 
     * @param  {Object|String} data
     * @return {Object}
     */
    function parsePreview(data){
       if (typeof data === 'string') {
            data = {
                items: data
            };
        }

        data.items = utils.stringToArray(data.items);

        return _.defaults(data, previewConfig);
    }

    return Tab.extend({

        /**
         * Extends instance with default config, calls initializes of parent class
         */
        initialize: function () {
            _.extend(this, defaults);

            _.bindAll(this, 'getPreview', 'buildPreview', 'hasPreview');

            __super__.initialize.apply(this, arguments);
        },

        /**
         * Calls initObservable of parent class, initializes observable
         *     properties of instance
         *     
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            __super__.initObservable.apply(this, arguments);

            this.observe({
                'noPreview': true,
                'body':      [],
                'head':      [],
                'indexed':   {}
            });

            return this;
        },

        /**
         * Calls initProperties of parent class, initializes properties 
         *     of instance
         *     
         * @return {Object} - reference to instance
         */
        initProperties: function () {
            __super__.initProperties.apply(this, arguments);

            this.displayed  = {};

            return this;
        },

        /**
         * Is being called when child element has been initialized,
         *     calls initElement of parent class, binds to element's update event,
         *     calls insertToArea and insertToIndexed methods passing element to it
         *
         * @param  {Object} elem
         */
        initElement: function (elem) {
            __super__.initElement.apply(this, arguments);

            this.insertToArea(elem)
                .insertToIndexed(elem);
        },

        /**
         * Inserts element to it's display area
         * 
         * @param  {Object} elem
         * @return {Object} - reference to instance
         */
        insertToArea: function (elem) {
            var regions = [];

            elem.displayArea = elem.displayArea || this.displayArea;

            regions = this.elems.groupBy('displayArea');

            _.each(regions, function (elems, region) {
                this[region](elems);
            }, this);

            return this;
        },

        /**
         * Adds element to observable indexed object of instance
         * 
         * @param  {Object} elem
         * @return {Object} - reference to instance
         */
        insertToIndexed: function (elem) {
            var indexed = this.indexed();
            
            indexed[elem.index] = elem;

            this.indexed(indexed);
            
            return this;
        },

        /**
         * Formats incoming previews array via parsePreview function
         * 
         * @param  {Array} previews
         * @return {Array} - formatted previews
         */
        formatPreviews: function(previews){
            return previews.map(parsePreview);
        },

        /**
         * Creates string view of previews
         * 
         * @param  {Object} data
         * @return {Strict} - formatted preview string
         */
        buildPreview: function(data){
            var preview = this.getPreview(data.items),
                prefix  = data.prefix;

            return prefix + preview.join(data.separator);
        },

        /**
         * Defines if instance has preview for incoming data
         * 
         * @param  {Object}  data
         * @return {Boolean}
         */
        hasPreview: function(data){
            return !!this.getPreview(data.items).length;
        },

        /**
         * Creates an array of previews for elements specified in incoming
         * items array, calls updatePreview afterwards.
         * 
         * @param  {Array} items - An array of element's indexes.
         * @returns {Array} An array of previews.
         */
        getPreview: function(items){
            var elems       = this.indexed(),
                displayed   = this.displayed,
                preview;

            items = items.map(function(index){
                var elem = elems[index];

                preview = elem ? elem.delegate('getPreview') : [];
                preview = _.compact(preview).join(', ');

                displayed[index] = !!preview;
                
                return preview;
            });

            this.updatePreview();

            return _.compact(items);
        },

        /**
         * Sets boolean value to noPreview observable based on whether display
         *     object contains truthy values
         */
        updatePreview: function(){
            this.noPreview(!_.some(this.displayed));
        }
    });
});