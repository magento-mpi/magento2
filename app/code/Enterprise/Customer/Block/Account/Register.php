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
        if (Mage::getStoreConfigFlag('enterprise_invitation/general/registration_required_invitation')) {
            return '';
        }
        return parent::_toHtml();
    }
}
