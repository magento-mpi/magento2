<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_CustomerCustomAttributes_Block_Account_Register extends Magento_Customer_Block_Account_Register
{
    protected function _toHtml()
    {
        $key = 'magento_invitation/general/registration_required_invitation';
        if ($this->_storeConfig->getConfigFlag($key)) {
            return '';
        }
        return parent::_toHtml();
    }
}
