/**
 * Backend client side validation rules
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {
    jQuery.validator.addMethod('required-synchronize', function(){
        storage = getConnectionName(
            jQuery('#system_media_storage_configuration_media_storage').val(),
            jQuery('#system_media_storage_configuration_media_database').val()
        );
        return allowedStorages.include(storage);
    }, 'Synchronization is required.');
})(jQuery);
