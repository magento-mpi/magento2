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
 * Adminhtml Report Sold Product Content Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Product_Sold extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize container block settings
     *
     */
    protected function _construct()
    {
        $this->_controller = 'report_product_sold';
        $this->_headerText = Mage::helper('Mage_Reports_Helper_Data')->__('Products Ordered');
        parent::_construct();
        $this->_removeButton('add');
    }
}
