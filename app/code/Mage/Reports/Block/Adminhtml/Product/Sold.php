<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend Report Sold Product Content Block
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Block_Adminhtml_Product_Sold extends Mage_Backend_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'Mage_Reports';

    /**
     * Initialize container block settings
     *
     */
    protected function _construct()
    {
        $this->_controller = 'report_product_sold';
        $this->_headerText = __('Products Ordered');
        parent::_construct();
        $this->_removeButton('add');
    }
}
