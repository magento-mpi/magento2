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
    'Magento_Ui/js/lib/events',
    'mage/utils'
], function(_, Collection, Scope, EventsBus, utils) {
    'use strict';

    var defaults = {
        label:      '',
        required:   false,
        template:   'ui/group/group',
        breakLine:  false
    };

    function getLabel(obj){
        var elems = obj.elems,
            label = obj.label;

        if(!label){
            elems.some(function(elem){
                return (label = elem.label);
            });
        }

        return label;
    }

    function getRequired(obj){
        var elems = obj.elems;

        return elems.some(function(elem){
            return elem.required();
        });
    }

    function getUid(obj){
        var elems = obj.elems,
            uid;

        elems.some(function(elem){
            uid = elem.uid;

            return typeof uid !== 'undefined';
        });

        return uid;
    }

    var FormGroup = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.uid = utils.uniqueid();

            this.extractData()
                .initListeners();
        },

        extractData: function(){
            return _.extend(this, {
                label:      getLabel(this),
                required:   getRequired(this),
                uid:        getUid(this)
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
            return this.elems.some(function(elem){
                return elem.hasChanged();
            });
        }
    }, EventsBus);

    return Collection(FormGroup);
});