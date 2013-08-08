<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Remember Me block
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Persistent_Block_Form_Remember extends Magento_Core_Block_Template
{
    /**
     * Prevent rendering if Persistent disabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $helper Mage_Persistent_Helper_Data */
        $helper = Mage::helper('Mage_Persistent_Helper_Data');
        return ($helper->isEnabled() && $helper->isRememberMeEnabled()) ? parent::_toHtml() : '';
    }

    /**
     * Is "Remember Me" checked
     *
     * @return bool
     */
    public function isRememberMeChecked()
    {
        /** @var $helper Mage_Persistent_Helper_Data */
        $helper = Mage::helper('Mage_Persistent_Helper_Data');
        return $helper->isEnabled() && $helper->isRememberMeEnabled() && $helper->isRememberMeCheckedDefault();
    }
}
