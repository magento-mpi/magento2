(function($) {
    $.fn.mageEventFormValidate = function(widget) {
        // Loop thru the elements that we jQuery validate is attached to
        // and return the loop, so jQuery function chaining will work.
        return this.each(function() {
            var form = $(this);
            // Grab this element's validator object (if it has one)
            var validator = form.data('validator');
            // Only run this code if there's a validator associated with this element
            if (!validator)
                return;
            // Only add these triggers to each element once
            if (form.data('mageEventFormValidate'))
                return;
            else
                form.data('mageEventFormValidate', true);
            // Override the function that validates the whole form to trigger a
            // formValidation event and either formValidationSuccess or formValidationError
            var oldForm = validator.form;
            validator.form = function() {
                oldForm.status = false;
                oldForm.currentForm = this.currentForm;
                widget._trigger('beforeValidation', 0, oldForm);
                if (!oldForm.status) {
                    oldForm.status = oldForm.apply(this, arguments);
                }
                widget._trigger('afterValidation', 0, oldForm);
                return oldForm.status;
            };
        });
    };
})(jQuery);