/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/lib/ko/scope'
], function(_, Scope) {
    'use strict';

    var defaults = {
        active: false
    };

    return Scope.extend({
        initialize: function(config, provider) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initListeners()
                .pushParams()
        },

        initObservable: function() {
            this.observe({
                'active':   this.active,
                'changed':  false
            });

            return this;
        },

        initListeners: function() {
            var params = this.provider.params;

            _.bindAll(this, 'pullParams', 'onAreasChange');

            params.on({
                'update:activeArea':    this.pullParams,
                'update:changedAreas':  this.onAreasChange
            });

            return this;
        },

        pushParams: function() {
            var params = this.provider.params;

            if(this.active()){
                params.set('activeArea', this.name);
            }
        },

        pullParams: function() {
            var params  = this.provider.params,
                area     = params.get('activeArea');

            this.active(area === this.name);
        },

        setActive: function() {
            this.active(true)

            this.pushParams();
        },

        onAreasChange: function(changed) {
            this.changed(!!~changed.indexOf(this.name));
        }
    });

});