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
 * Adminhtml tags detail for product report blocks content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Report_Tag_Product_Detail extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'report_tag_product_detail';

        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($this->getRequest()->getParam('id'));

        $this->_headerText = Mage::helper('Mage_Reports_Helper_Data')->__('Tags submitted to %s', $product->getName());
        parent::_construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_tag/product/'));
        $this->_addBackButton();
    }

}
