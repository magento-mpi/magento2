<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Customer_Block_Account_Register extends Mage_Customer_Block_Account_Register
{
    public function _toHtml()
    {
        $key = 'enterprise_invitation/general/registration_required_invitation';
        if ($this->_storeConfig->getConfigFlag($key)) {
            return '';
        }
        return parent::_toHtml();
    }
}
