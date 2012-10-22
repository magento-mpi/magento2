/**
 *
 * @license     {}
 */
(function($) {
    $.widget("mage.form", {
        options: {
            actions: {
                saveAndContinueEdit: {
                    template: '${base}{{each(key, value) args}}${key}/${value}/{{/each}}',
                    args: {'back': 'edit'}
                }
            }
        },
        /**
         * Form creation
         * @protected
         */
        _create: function() {
            $.each(this.options.actions, function(i, v){
                $.template(i, v.template);
            })
            this._bind();
        },
        /**
         * Bind '_submit' method on 'save' and 'saveAndContinueEdit' events
         * @protected
         */
        _bind: function() {
            this.element.on('save saveAndContinueEdit', $.proxy(this._submit, this));
        },
        /**
         * Get action url for form
         * @param {string} name of action
         * @param {object} object with parameters for action url
         * @return {string|boolean}
         */
        _getActionUrl: function(action, data){
            var actions = this.options.actions;
            if (actions[action]) {
                return $.tmpl(action, $.extend({
                    base: this.element.attr('action'),
                    args: data || action.args || {}
                }));
            }
            return false;
        },
        /**
         * Submit the form
         * @param {object} event object
         * @param {object} event data object
         * @return {string|boolean}
         */
        _submit: function(e, data) {
            var url = this._getActionUrl(e.type, data);
            if (url) {
                this.element.attr('action', url);
            }
            this.element.triggerHandler('submit');
        }
    });

    $.widget('ui.button', $.ui.button, {
        /**
         * Button creation
         * @protected
         */
        _create: function(){
            var data = this.element.data().widgetButton;
            if($.type(data) === 'object') {
                this.element.on('click', function(){
                    $(data.related).trigger(data.event);
                })
            }
            this._super("_create");
        }
    })
})(jQuery);

