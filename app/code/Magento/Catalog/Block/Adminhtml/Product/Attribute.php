<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Product;

/**
 * Adminhtml catalog product attributes block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Attribute extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_product_attribute';
        $this->_blockGroup = 'Magento_Catalog';
        $this->_headerText = __('Product Attributes');
        $this->_addButtonLabel = __('Add New Attribute');
        parent::_construct();
    }

}
