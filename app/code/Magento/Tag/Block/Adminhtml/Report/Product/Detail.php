<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tags detail for product report blocks content block
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Report_Product_Detail extends Magento_Backend_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Tag';
        $this->_controller = 'adminhtml_report_product_detail';

        $product = Mage::getModel('Magento_Catalog_Model_Product')->load($this->getRequest()->getParam('id'));

        $this->_headerText = __('Tags submitted to %1', $product->getName());
        parent::_construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_tag/product/'));
        $this->_addBackButton();
    }
}
