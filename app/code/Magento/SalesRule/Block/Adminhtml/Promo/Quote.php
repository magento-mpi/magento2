<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Block\Adminhtml\Promo;

/**
 * Catalog price rules
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Quote extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'promo_quote';
        $this->_headerText = __('Shopping Cart Price Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
    }
}
