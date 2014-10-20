define([
    './components/types',
    './components/layout',
    'Magento_Ui/js/lib/class'
], function(Types, Layout, Class){
    'use strict';

    return Class.extend({
        initialize: function(data){
            this.types = new Types(data.types);
            this.layout = new Layout(data.layout, this.types);
        },

        render: function(data){
            this.layout.process(data.layout);
            this.types.set(data.types);
        }
    });
});