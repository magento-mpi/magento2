<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tags detail for product report blocks content block
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Block_Adminhtml_Report_Product_Detail extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'Mage_Tag';
        $this->_controller = 'adminhtml_report_product_detail';

        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($this->getRequest()->getParam('id'));

        $this->_headerText = Mage::helper('Mage_Tag_Helper_Data')->__('Tags submitted to %s', $product->getName());
        parent::__construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_tag/product/'));
        $this->_addBackButton();
    }

}
