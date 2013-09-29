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
namespace Magento\Reports\Block\Adminhtml\Product;

class Sold extends \Magento\Backend\Block\Widget\Grid\Container
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
