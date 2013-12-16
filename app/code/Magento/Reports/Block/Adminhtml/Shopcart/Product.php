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
 * Adminhtml Shopping cart products report page content block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Adminhtml\Shopcart;

class Product extends \Magento\Backend\Block\Widget\Grid\Container
{

    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reports';
        $this->_controller = 'adminhtml_shopcart_product';
        $this->_headerText = __('Products in carts');
        parent::_construct();
        $this->_removeButton('add');
    }

}
