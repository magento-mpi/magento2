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
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setRedirectUrl(Mage::registry(Phoenix_Moneybookers_ProcessingController::REGISTRY_REDIRECT_URL_KEY));
        return parent::_prepareLayout();
    }
}
