/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'underscore',
    'Magento_Ui/js/initializer/collection',
    'Magento_Ui/js/lib/ko/scope',
    'Magento_Ui/js/lib/events',
    'mage/utils'
], function(_, Collection, Scope, EventsBus, utils) {
    'use strict';

    var defaults = {
        label:      '',
        required:   false,
        template:   'ui/group/group',
        breakLine:  false,
        invalid: []
    };

    function getLabel(elems, obj){
        var label = obj.label;

        if(!label){
            elems.some(function(elem){
                return (label = elem.label);
            });
        }

        return label;
    }

    function getRequired(elems){
        return elems.some(function(elem){
            return elem.required();
        });
    }

    function getUid(elems){
        var uid;

        elems.some(function(elem){
            return (uid = elem.uid);
        });

        return uid;
    }

    var FormGroup = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initListeners()
                .extractData()
                .update();
        },

        initObservable: function () {
            this.observe('invalid', this.invalid || []);

            return this;
        },

        extractData: function(){
            var elems = this.elems;

            return _.extend(this, {
                label:      getLabel(elems, this),
                required:   getRequired(elems, this),
                uid:        getUid(elems, this)
            });
        },

        validate: function (element) {
            return !element.isValid();
        },

        update: function (value, name) {
            this.invalid(this.elems.filter(this.validate));
            
            this.trigger('update', this.name, {
                name: name,
                value: value
            });

            return this;
        },

        initListeners: function(){
            this.elems.forEach(function(elem){
                elem.on('update', this.update.bind(this));
            }, this);
            
            return this;
        },

        getTemplate: function(){
            return this.template;
        },

        hasChanged: function(){
            return this.elems.some(function(elem){
                return elem.hasChanged();
            });
        }
    }, EventsBus);

    return Collection(FormGroup);
});