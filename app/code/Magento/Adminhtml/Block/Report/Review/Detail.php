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
 * Adminhtml report review product blocks content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Report_Review_Detail extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'report_review_detail';

        $product = Mage::getModel('Magento_Catalog_Model_Product')->load($this->getRequest()->getParam('id'));
        $this->_headerText = __('Reviews for %1', $product->getName());

        parent::_construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_review/product/'));
        $this->_addBackButton();
    }

}
