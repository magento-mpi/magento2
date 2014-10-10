/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/initializer/collection',
    'Magento_Ui/js/lib/ko/scope'
], function(_, Collection, Scope) {
    'use strict';

    var defaults = {
        visible: false,
        label:   ''
    };

    var Area = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);
            
            this.initObservable()
                .initListeners()
                .pullParams();

            console.log('Area.js this=', this);
        },

        initObservable: function() {
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
                area    = params.get('activeArea');

            this.visible(area === this.name);

            return this;
        },

        update: function(changed){
            var params  = this.provider.params,
                areas   = params.get('changedAreas') || [];

            areas = changed ?
                _.union(areas, [this.name]) :
                _.without(areas, this.name);

            params.set('changedAreas', areas);
        },

        getTemplate: function () {
            return 'ui/area';
        }
    });

    return Collection(Area);
});