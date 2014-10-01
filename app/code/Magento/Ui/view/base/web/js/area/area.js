/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/lib/collection',
    'Magento_Ui/js/lib/ko/scope'
], function(_, Collection, Scope) {
    'use strict';

    var defaults = {
        visible: false
    };

    var Area = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initListeners()
                .pullParams()
                .waitElements();
        },

        initObservable: function() {
            this.observe({
                'elems':    [],
                'visible':  this.visible
            });

            return this;
        },

        initElements: function(){
            var elems = this.elems;

            elems.push.apply(elems, arguments);

            elems().forEach(function(elem){
                elem.on({
                    'change': this.update.bind(this, true),
                    'restore': this.update.bind(this, false)
                });
            }, this);

            return this;
        },

        initListeners: function() {
            var params = this.provider.params;

            params.on('update:activeTab', this.pullParams.bind(this));

            return this;
        },

        pullParams: function() {
            var params  = this.provider.params,
                area    = params.get('activeTab');

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
        }
    });

    return Collection(Area);
});