<?php
/**
 * {license_notice}
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Phoenix_Moneybookers_Block_Redirect extends Mage_Core_Block_Template
{
    /**
     * Get redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        if ($url = Mage::registry(Phoenix_Moneybookers_ProcessingController::REGISTER_FORM_REDIRECT_URL_KEY)) {
            return $url;
        }
        return '';
    }
}
