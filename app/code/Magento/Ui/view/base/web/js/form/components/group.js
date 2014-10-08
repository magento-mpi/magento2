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

    function isValid(element) {
        return !element.isValid();
    }

    function getName(element) {
        return element.name;
    }

    var FormGroup = Scope.extend({
        initialize: function(config) {
            _.extend(this, defaults, config);

            this.initObservable()
                .initListeners()
                .extractData();
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

        validate: function () {
            var params                  = this.provider.params,
                elements                = this.elems,
                previousInvalidNames    = params.get('invalid'),
                invalidElements         = [],
                invalidNames            = [];

            invalidElements = elements.filter(isValid);
            invalidNames = invalidElements.map(getName);
            invalidNames = _.uniq(invalidNames.concat(previousInvalidNames));

            this.invalid(invalidElements);
            params.set('invalid', invalidNames);

            return this;
        },

        initListeners: function(){
            var update      = this.trigger.bind(this, 'update'),
                validate    = this.validate.bind(this);

            this.elems.forEach(function(elem){
                elem.on('update', update);
            }, this);

            this.provider.params.on('update:validated', validate);

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