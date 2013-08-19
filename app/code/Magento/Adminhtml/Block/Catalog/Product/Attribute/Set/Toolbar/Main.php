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
 * Adminhtml catalog product sets main page toolbar
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Main extends Magento_Adminhtml_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/attribute/set/toolbar/main.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('addButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Add New Set'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/add') . '\')',
            'class' => 'add',
        ));
        return parent::_prepareLayout();
    }

    public function getNewButtonHtml()
    {
        return $this->getChildHtml('addButton');
    }

    protected function _getHeader()
    {
        return __('Product Templates');
    }

    protected function _toHtml()
    {
        Mage::dispatchEvent('adminhtml_catalog_product_attribute_set_toolbar_main_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}
