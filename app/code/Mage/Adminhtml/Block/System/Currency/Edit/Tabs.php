<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency edit tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Currency_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('currency_edit_tabs');
        $this->setDestElementId('currency_edit_form');
        $this->setTitle(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Currency'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('General Information'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_System_Currency_Edit_Tab_Main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('currency_rates', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Rates'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_System_Currency_Edit_Tab_Rates')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}
