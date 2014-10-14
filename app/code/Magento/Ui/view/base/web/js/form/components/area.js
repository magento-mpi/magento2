/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/initializer/collection',
    '../component'
], function(_, Collection, Component) {
    'use strict';

    var defaults = {
        template: 'ui/area',
        visible: false
    };

    var __super__ = Component.prototype;

    var Area = Component.extend({
        initialize: function() {
            _.extend(this, defaults);
            
            __super__.initialize.apply(this, arguments);

            this.pullParams();
        },

        initObservable: function() {
            __super__.initObservable.apply(this, arguments);

            this.observe({
                'visible': this.visible
            });

            return this;
        },

        initListeners: function() {
            var params  = this.provider.params,
                update  = this.update,
                handlers;

            handlers = {
                'change':   update.bind(this, true),
                'restore':  update.bind(this, false)
            };

            params.on('update:activeArea', this.pullParams.bind(this));

            this.elems.forEach(function(elem){
                elem.on(handlers);
            });

            return this;
        },

        pullParams: function() {
            var params  = this.provider.params,
                area    = params.get('activeArea'),
                visible = area === this.name;

            this.trigger('active', visible)
                .visible(visible);
                
            return this;
        },

        update: function(changed, element, settings){
            var params  = this.provider.params,
                areas   = params.get('changedAreas') || [];

            areas = changed ?
                _.union(areas, [this.name]) :
                _.without(areas, this.name);

            params.set('changedAreas', areas);

            if (settings.makeVisible) {
                params.set('activeArea', this.name);
            }
        }
    });

    return Collection(Area);
});