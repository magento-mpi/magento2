/**
 * {license_notice}
 *
 * @category    frontend gift registry
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.shareGiftRegistry', {
        /**
         * Widget options.
         * @type {Object} - An object containing options including a counter generating unique recipient IDs.
         */
        options: {
            addRecipientTmpl: '#add-recipient-tmpl', // Template for adding a new recipient.
            removeRecipientTmpl: '#remove-recipient-tmpl', // Template for removing a recipient.
            addRecipientButton: '#add-recipient-button', // Button selector for adding a new recipient.
            field: 'div.field', // Selector for a container in the add recipient template having an input field.
            recipientsList: '#recipients-options', // Selector for the list of recipients.
            maxRecipientsMessage: '#max-recipient-message', // Selector for the max recipients message.
            removeRecipientLink: 'a.btn-remove', // Selector for the link anchor used to remove a recipient.
            recipientCounter: 0 // Running counter used for tracking recipients and generating unique IDs.
        },

        /**
         * Bind a click event handler to the add recipient button.
         * @private
         */
        _create: function() {
            $(this.options.addRecipientButton).on('click', $.proxy(function() {
                // Call explicitly with no arguments so that name and email will be undefined.
                this.addRecipient();
            }, this));
        },

        /**
         * Remove a selected recipient from the recipients list in the share gift registry form.
         * @private
         * @param event {Event} - Click event. Event target is the remove recipient link anchor image.
         */
        _removeRecipient: function(event) {
            var recipientCount = $(this.options.recipientsList).find('li:not(.no-display)').length;
            $(event.target).closest('li').remove(); // Remove the recipient list item from the recipients list.
            if (--recipientCount < this.options.maxRecipients && this.options.maxRecipients !== 0) {
                // Show the add recipient button when fewer than the maximum number of recipients have been added
                // and hide the maximum recipients message.
                $(this.options.addRecipientButton).show();
                $(this.options.maxRecipientsMessage).hide();
            }
        },

        /**
         * Add a new recipient to the recipients list in the share gift registry form. Ensure unique ID
         * values by using a widget option to increment a running counter.
         * @param name ({string|undefined}) - The recipient's name.
         * @param email ({string|undefined}) - The recipient's email address.
         */
        addRecipient: function(name, email) {
            name = name || '';
            email = email || '';

            var recipientCount = $(this.options.recipientsList).find('li:not(.no-display)').length,
                // Create the new recipient list item from the add recipient template.
                li = $(this.options.addRecipientTmpl).tmpl({
                    addRow: recipientCount > 0 ? 'add-row' : '',
                    recipient: this.options.recipientCounter++,
                    name: name, nameMessage: this.options.recipientNameMessage,
                    email: email, emailMessage: this.options.recipientEmailMessage
                });

            if (recipientCount > 0) {
                // Add the remove HTML, which consists of a link anchor with a small 'X' icon that can be
                // clicked to remove the selected recipient.
                $(this.options.removeRecipientTmpl).tmpl({
                    image: this.options.removeRecipientImage, alt: this.options.removeRecipientMessage
                }).insertBefore(li.find(this.options.field).first());
                // Add a click handler to the remove link anchor so that the new recipient can be removed.
                li.find(this.options.removeRecipientLink).on('click', $.proxy(function(event) {
                    event.preventDefault(); // Prevent the click event from going to the link anchor's href.
                    this._removeRecipient(event);
                }, this));
            }

            if (++recipientCount >= this.options.maxRecipients && this.options.maxRecipients !== 0) {
                // Remove the add recipient button when maximum recipients have been added and show the
                // maximum recipients message.
                $(this.options.addRecipientButton).hide();
                $(this.options.maxRecipientsMessage).show();
            }

            // Finally, add the new recipient list item to the recipients list.
            li.appendTo(this.options.recipientsList);
        }
    });
})(jQuery);
