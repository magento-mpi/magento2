/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
$j = jQuery.noConflict();
$j(document).ready(function(){
    jQuery.validator.addClassRules("required-entry", {
        required: true
    });
})