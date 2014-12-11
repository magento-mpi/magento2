<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Block\Adminhtml\Report\Customer;

/**
 * Wishlist report block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Wishlist extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_MultipleWishlist';
        $this->_controller = 'adminhtml_report_customer_wishlist';
        $this->_headerText = __("Customer's Wish List Report");
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
