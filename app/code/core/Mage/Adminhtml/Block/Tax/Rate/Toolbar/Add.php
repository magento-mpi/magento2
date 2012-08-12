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
 * Admin tax class product toolbar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tax_Rate_Toolbar_Add extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('tax/toolbar/rate/add.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('addButton',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label' => Mage::helper('Mage_Tax_Helper_Data')->__('Add New Tax Rate'),
                    'onclick' => 'window.location.href=\''.$this->getUrl('*/tax_rate/add').'\'',
                    'class' => 'add'
                ))
        );
        return parent::_prepareLayout();
    }
}
