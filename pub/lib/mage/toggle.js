/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    "use strict";

    $.widget("mage.toggle", {

        options: {
            baseToggleClass: "active"      // Class used to be toggled on clicked element
        },

        /**
         * Toggle creation
         * @private
         */
        _create: function() {
            this._beforeCreate();
            this.element.on('click', $.proxy(this._onClick, this));
            this._afterCreate();
        },

        /**
         * Binding Click event
         *
         * @private
         * @param {Object} event
         */
        _onClick: function(event) {
            this._toggleSelectors();
            return false;               //Prevent default click for links and submit buttons
        },

        /**
         * Method responsible for hiding and revealing specified DOM elements
         * Toggle the class on clicked element
         *
         * @private
         */
        _toggleSelectors: function () {
            this.element.toggleClass(this.options.baseToggleClass);
        },

        /**
         * Method used to inject 3rd party functionality before create
         * @private
         */
        _beforeCreate: function() {},

        /**
         * Method used to inject 3rd party functionality after create
         * @private
         */
        _afterCreate: function() {},
    });

    // Extension for mage.toggle - Adding selectors support for other DOM elements we wish to toggle
    $.widget('mage.toggle', $.mage.toggle, {

        options: {
            selectorsToggleClass: "hidden"    // Class used to be toggled on selectors DOM elements
        },

        /**
         * Method responsible for hiding and revealing specified DOM elements
         * If data-toggle-selectors attribute is present - toggle will be done on these selectors
         * Otherwise we toggle the class on clicked element
         *
         * @private
         * @override
         */
        _toggleSelectors: function () {
            if (this.element.data('toggle-selectors')) {
                $(this.element.data('toggle-selectors')).toggleClass(this.options.selectorsToggleClass);
            } else {
                this.element.toggleClass(this.options.baseToggleClass);
            }
        }

    });

    // Extension for mage.toggle - Adding label toggle
    $.widget('mage.toggle', $.mage.toggle, {

        /**
         * Binding Click event
         *
         * @private
         * @override
         */
        _onClick: function() {
            this._toggleLabel();
            this._super();
        },

        /**
         * Method responsible for replacing clicked element labels
         * @protected
         */
        _toggleLabel: function() {
            if (this.element.data('toggle-label')) {
                var currentLabelSelector = (this.element.data('current-label-el')) ?
                        $(this.element.data('current-label-el')) : this.element,
                    newLabel = this.element.data('toggle-label');
                this.element.data('toggle-label', currentLabelSelector.html());
                currentLabelSelector.html(newLabel);
            }
        }
    });

})(jQuery);
