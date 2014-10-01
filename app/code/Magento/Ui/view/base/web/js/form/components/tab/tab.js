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

    var Tab = Scope.extend({
        initialize: function(config, provider) {
            _.extend(this, config);

            this.provider = provider;

            this.initObservable()
                .initListeners()
                .pushParams()
        },

        initObservable: function() {
            this.observe({
                'active': this.active,
                'changed': false
            });

            return this;
        },

        initListeners: function() {
            var params = this.provider.params;

            params.on({
                'update:activeTab':     this.pullParams.bind(this),
                'update:changedAreas':  this.onAreasChange.bind(this)
            });

            return this;
        },

        setActive: function() {
            this.active(true)

            this.pushParams();
        },

        pushParams: function() {
            var params = this.provider.params;

            if(this.active()){
                params.set('activeTab', this.name);
            }
        },

        pullParams: function() {
            var params  = this.provider.params,
                tab     = params.get('activeTab');

            this.active(this.name === tab);
        },

        onAreasChange: function(changed){
            this.changed( !!~changed.indexOf(this.name) );
        }
    });

    return Tab;
});