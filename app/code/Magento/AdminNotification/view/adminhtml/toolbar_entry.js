/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    'use strict';
    $(document).ready(function() {
        // Mark notification as read via AJAX call
        var markNotificationAsRead = function(notificationId) {
            var requestUrl = $('.notifications .dropdown-menu').attr('data-mark-as-read-url');
            $.ajax({
                url: requestUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    id: notificationId
                },
                showLoader: false
            });
        };

        // Remove notification from the list
        var removeNotificationFromList = function(notificationEntry) {
            notificationEntry.remove();
            var notificationCount = $('.notifications').attr('data-notification-count');
            notificationCount--;
            $('.notifications').attr('data-notification-count', notificationCount);

            if (notificationCount == 0) {
                // Change appearance of the bubble and its behavior when the last notification is removed
                $('.notifications .dropdown-menu').remove();
                var notificationIcon = $('.notifications .notifications-icon');
                notificationIcon.removeAttr('data-toggle');
                notificationIcon.off('click.dropdown');
                $('.notifications .notifications-icon .value').text('');
            } else {
                $('.notifications .notifications-icon .value').text(notificationCount);
                // Modify caption of the 'See All' link
                var actionElement = $('.notifications .dropdown-menu .last .action-more');
                actionElement.text(actionElement.text().replace(/\d+/, notificationCount));
            }
        };

        // Show popup with notification details
        var showNotificationDetails = function(notificationEntry) {
            var popupElement = notificationEntry.find('.notification-dialog-content').clone();
            var notificationId = notificationEntry.attr('data-notification-id');
            var dialogClassSeverity = 'notification-entry-dialog';
            if (notificationEntry.attr('data-notification-severity')) {
                dialogClassSeverity = 'notification-entry-dialog notification-entry-dialog-critical';
            }
            popupElement.dialog({
                title: popupElement.attr('data-title'),
                minWidth: 500,
                modal: true,
                dialogClass: dialogClassSeverity,
                buttons: [
                    {
                        text: popupElement.attr('data-acknowledge-caption'),
                        'class': 'action-acknowledge primary',
                        click: function(event) {
                            markNotificationAsRead(notificationId);
                            removeNotificationFromList(notificationEntry);
                            $(this).dialog('close');
                        }
                    },
                    {
                        text: popupElement.attr('data-cancel-caption'),
                        'class': 'action-cancel',
                        click: function(event) {
                            $(this).dialog('close');
                        }
                    }
                ]
            });
            popupElement.parent().attr('aria-live','assertive');
            popupElement.dialog('open');
        };

        // Show notification description when corresponding item is clicked
        $('.notifications .dropdown-menu .notification-entry').on('click.showNotification', function(event) {
            // hide notification dropdown
            $('.notifications .notifications-icon').trigger('click.dropdown');
            showNotificationDetails($(this));
            event.stopPropagation();
        });

        // Remove corresponding notification from the list and mark it as read
        $('.notifications .dropdown-menu .notification-entry .action-close').on('click.removeNotification', function(event) {
            var notificationEntry = $(this).closest('.notification-entry')
            var notificationId = notificationEntry.attr('data-notification-id');
            markNotificationAsRead(notificationId);
            removeNotificationFromList(notificationEntry);
            event.stopPropagation();
        });
    });
})(window.jQuery);
