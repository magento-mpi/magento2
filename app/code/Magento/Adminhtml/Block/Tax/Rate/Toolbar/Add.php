<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin tax class product toolbar
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Tax_Rate_Toolbar_Add extends Magento_Adminhtml_Block_Template
{

    protected $_template = 'tax/toolbar/rate/add.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('addButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Add New Tax Rate'),
            'onclick' => 'window.location.href=\''.$this->getUrl('*/tax_rate/add').'\'',
            'class' => 'add'
        ));
        return parent::_prepareLayout();
    }
}
