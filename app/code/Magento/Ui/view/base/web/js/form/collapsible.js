/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    './component'
], function(_, Component) {
    'use strict';

    var defaults = {
        collapsible:    false,
        opened:         true
    };

    var __super__ = Component.prototype;

    return Component.extend({
        initialize: function() {
            _.extend(this, defaults);

            __super__.initialize.apply(this, arguments);
        },

        initObservable: function(){
            __super__.initObservable.apply(this, arguments);

            this.observe({
                'opened': this.opened
            });

            return this;
        },

        toggle: function() {
            var opened = this.opened;

            opened(!opened());

            this.trigger('active', opened());

            return this;
        },

        onClick: function(){
            if(this.collapsible){
                this.toggle();
            }
        }
    });
});