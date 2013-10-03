<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist report block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block\Adminhtml\Report\Customer;

class Wishlist
    extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_MultipleWishlist';
        $this->_controller = 'adminhtml_report_customer_wishlist';
        $this->_headerText = __("Customer's Wish List Report");
        parent::_construct();
        $this->_removeButton('add');
    }
}
