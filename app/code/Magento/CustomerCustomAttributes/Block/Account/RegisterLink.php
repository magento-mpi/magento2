<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Magento_CustomerCustomAttributes_Block_Account_RegisterLink
 */
class Magento_CustomerCustomAttributes_Block_Account_RegisterLink extends Magento_Customer_Block_Account_RegisterLink
{
    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        $key = 'magento_invitation/general/registration_required_invitation';
        if ($this->_storeConfig->getConfigFlag($key)) {
            return '';
        }
        return parent::_toHtml();
    }
}
