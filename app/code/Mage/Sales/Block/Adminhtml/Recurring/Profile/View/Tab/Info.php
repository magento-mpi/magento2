<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile information tab
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile_View_Tab_Info
    extends Magento_Adminhtml_Block_Widget
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Profile Information');
    }

    /**
     * Also label getter :)
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
