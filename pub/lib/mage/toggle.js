/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($){
    $.widget("mage.toggle", {
        /**
         * Toggle creation
         * @private
         */
        _create: function() {
            this.element.on('click', $.proxy(this._onClick, this));
        }

        /**
         * Binding Click event
         * Click relies on following data attributes for customization:
         *  'data-toggle-selectors' - contains a comma separated list of CSS selectors which will be revealed or hidden
         *                            when click event occurs
         *  'data-current-label-el' - contains CSS selector for the element containing current label
         *  'data-toggle-label'     - contains label which will be used instead of current label upon click
         * @protected
         */
        , _onClick: function() {
            if (this.element.data('toggle-label')) {
                this._toggleLabel();
            }
            if (this.element.data('toggle-selectors')) {
                this._toggleSelectors();
            } else {
                this.element.toggle();
            }
            return false;
        }

        /**
         * Method responsible for replacing clicked element labels
         * @protected
         */
        , _toggleLabel: function() {
            var _currentLabelSelector = (this.element.data('current-label-el')) ? $(this.element.data('current-label-el')) : this.element;
            var _newLabel = this.element.data('toggle-label');
            this.element.data('toggle-label', _currentLabelSelector.html());
            _currentLabelSelector.html(_newLabel);
        }

        /**
         * Method responsible for hiding and revealing specified DOM elements
         * @protected
         */
        , _toggleSelectors: function () {
            $(this.element.data('toggle-selectors')).toggleClass('hidden');
        }
    });
})(jQuery);
