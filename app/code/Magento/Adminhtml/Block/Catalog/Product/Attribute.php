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
 * Adminhtml catalog product attributes block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Catalog_Product_Attribute extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'catalog_product_attribute';
        $this->_headerText = __('Product Attributes');
        $this->_addButtonLabel = __('Add New Attribute');
        parent::_construct();
    }

}
