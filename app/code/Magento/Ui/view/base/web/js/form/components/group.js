/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/lib/collection',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/events'
], function(_, Collection, Scope, EventsBus) {
    'use strict';

    var defaults = {
        label:      '',
        required:   false,
        template:   'ui/group/group'
    };

    function getLabel(obj){
        var elems = obj.elems,
            label = obj.label;

        if(!label){
            elems.some(function(elem){
                return (label = elem.meta.label);
            });
        }

        return label;
    }

    function getRequired(obj){
        var elems = obj.elems;

        return elems.some(function(elem){
            return elem.meta.required;
        });
    }

    var FormGroup = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.extractData()
                .initListeners();
        },

        extractData: function(){
            return _.extend(this, {
                label:      getLabel(this),
                required:   getRequired(this)
            });
        },

        initListeners: function(){
            var elems   = this.elems,
                trigger = this.trigger.bind(this, 'update');

            elems.forEach(function(elem){
                elem.on('update', trigger);
            });
        },

        getTemplate: function(){
            return this.template;
        },

        hasChanged: function(){
            var elems = this.elems;

            return elems.some(function(elem){
                return elem.hasChanged();
            });
        }
    }, EventsBus);

    return Collection(FormGroup);
});