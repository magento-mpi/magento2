/**
 * {license_notice}
 *
 * @category    Rma
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.templateBuilder', {

        /**
         * options with default values for setting up the template
         */
        options: {
            //Default template options
            registrantTemplate: '#template-registrant',
            registrantContainer: '#registrant-container',
            //Row count of the template rows
            liIndex: 0,
            rowContainer: '<li></li>',
            rowContainerClass: 'fields',
            addRegistrantBtn: 'add-registrant-button',
            btnRemoveIdPrefix: 'btn-remove',
            btnRemoveClass: 'btn-remove',
            rowIdPrefix: 'row',
            //This class is added to rows added after the first one. Adds the dotted separator
            additionalRowClass: 'add-row',
            /*
             This is provided during widget instantiation. eg :
             formDataPost : {"formData":formData,"templateFields":['field1-name','field2-name'] }
             -"formData" is the multi-dimensional array of form field values : [['a','b'],['c','b']]
             received from the server and encoded
             -"templateFields" are the input fields in the template with index suffixed after the field name
             eg field1-name{index}
             */
            formDataPost: null,
            //Default selectors for add and remove markup elements of a template
            clickEventSelectors: 'a, button',
            //This option allows adding first row delete option and a row separator
            hideFirstRowDelAddSeparator: true,
            //Max registrants - This option should be set when instantiating the widget
            maxRegistrant: 1000,
            maxRegistrantsMessage: '#max-registrant-message'
        },

        /**
         * Initialize create
         * @private
         */
        _create: function() {
            //On document ready related tasks
            $($.proxy(this.ready, this));

            //Binding template-wide events handlers for adding and removing rows
            this.element.on('click', this.options.clickEventSelectors, $.proxy(this.handleClick, this));
        },

        /**
         * Initialize template
         * @public
         */
        ready: function() {
            if (this.options.formDataPost.formData) {
                this.processFormDataArr(this.options.formDataPost);
            } else if (this.options.liIndex === 0 && this.options.maxRegistrant !== 0) {
                //If no form data , then add default row
                this.addRegistrant(0);
            }
        },

        /**
         * Process and loop through all registrant data to create preselected values. This is used for any error on submit.
         * For complex implementations the inheriting widget can override this behavior
         * @public
         * @param {Object} formDataArr
         */
        processFormDataArr: function(formDataArr) {
            var formData = formDataArr.formData,
                templateFields = formDataArr.templateFields;
            for (var i = 0; i < formData.length; i++) {
                this.addRegistrant(i);
                var formRow = formData[i];
                for (var j = 0; j < formRow.length; j++) {
                    this.setFieldById(templateFields[j] + i, formRow[j]);
                }
            }

        },

        /**
         * Initialize and create markup for template row. Add it to the parent container.
         * The template processing will substitute row index at all places marked with _index_ in the template
         * using the template
         * @public
         * @param {string} index - current index/count of the created template. This will be used as the id
         * @return {*}
         */
        addRegistrant: function(index) {
            var li = $(this.options.rowContainer);
            li.addClass(this.options.rowContainerClass).attr('id', this.options.rowIdPrefix + index);
            $(this.options.registrantTemplate).tmpl([
                {_index_: index}
            ]).appendTo(li);
            $(this.options.registrantContainer).append(li);
            li.addClass(this.options.additionalRowClass);
            //Hide 'delete' link and remove additionalRowClass for first row
            if (this.options.liIndex === 0 && this.options.hideFirstRowDelAddSeparator) {
                $('#' + this.options.btnRemoveIdPrefix + '0').hide();
                $('#' + this.options.rowIdPrefix + '0').removeClass(this.options.additionalRowClass);
            }
            this.maxRegistrantCheck(++this.options.liIndex);
            return li;
        },

        /**
         * Remove return item information row
         * @public
         * @param {string} liIndex - return item information row index
         * @return {boolean}
         */
        removeRegistrant: function(liIndex) {
            $('#' + this.options.rowIdPrefix + liIndex).remove();
            this.maxRegistrantCheck(--this.options.liIndex);
            return false;
        },

        /**
         * Function to check if maximum registrants are exceeded and render/hide maxMsg and Add btn
         * @public
         * @param liIndex
         */
        maxRegistrantCheck: function(liIndex) {
            var addRegBtn = $('#' + this.options.addRegistrantBtn),
                maxRegMsg = $(this.options.maxRegistrantsMessage);
            //liIndex starts from 0
            if (liIndex >= this.options.maxRegistrant) {
                addRegBtn.hide();
                maxRegMsg.show();
            } else if (!addRegBtn.is(":visible")) {
                addRegBtn.show();
                maxRegMsg.hide();
            }
        },

        /**
         * Set the value on given element
         * @public
         * @param {string} domId
         * @param {string} value
         */
        setFieldById: function(domId, value) {
            var x = $('#' + this._esc(domId));
            if (x.length) {
                if (x.is(':checkbox')) {
                    x.attr('checked', true);
                } else if (x.is('option')) {
                    x.attr('selected', 'selected');
                } else {
                    x.val(value);
                }
            }
        },

        /**
         * Delegated handler for click
         * @public
         * @param {Object} e - Native event object
         * @return {(null|boolean)}
         */
        handleClick: function(e) {
            var currElem = $(e.currentTarget);
            if (currElem.attr('id') === this.options.addRegistrantBtn) {
                this.addRegistrant(this.options.liIndex);
                return false;
            } else if (currElem.hasClass(this.options.btnRemoveClass)) {
                //Extract index of remove element
                this.removeRegistrant(currElem.closest("[id^='" + this.options.btnRemoveIdPrefix + "']")
                    .attr('id').replace(this.options.btnRemoveIdPrefix, ''));
                return false;
            }
        },

        /*
         * Utility function to add escape chars for jquery selector strings
         * @private
         * @param str - string to be processed
         * @return {string}
         */
        _esc: function(str) {
            if (str) {
                return str.replace(/([ ;&,.+*~\':"!\^$\[\]()=>|\/@])/g, '\\$1');
            } else {
                return str;
            }
        }
    });

})(jQuery);