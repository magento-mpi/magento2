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
 * Adminhtml catalog product attributes block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'catalog_product_attribute';
        $this->_headerText = Mage::helper('Mage_Catalog_Helper_Data')->__('Product Attributes');
        $this->_addButtonLabel = Mage::helper('Mage_Catalog_Helper_Data')->__('Add New Attribute');
        parent::_construct();
    }

}
