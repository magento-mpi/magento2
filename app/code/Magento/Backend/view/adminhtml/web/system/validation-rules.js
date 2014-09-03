/**
 * Backend client side validation rules
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
	"jquery",
	"jquery/validate"
], function(jQuery){

    jQuery.validator.addMethod('required-synchronize', function(){
        storage = getConnectionName(
            jQuery('#system_media_storage_configuration_media_storage').val(),
            jQuery('#system_media_storage_configuration_media_database').val()
        );
        return allowedStorages.include(storage);
    }, 'Synchronization is required.');

});