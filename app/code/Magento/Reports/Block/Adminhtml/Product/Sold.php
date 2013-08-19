<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend Report Sold Product Content Block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Block_Adminhtml_Product_Sold extends Magento_Backend_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'Magento_Reports';

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
