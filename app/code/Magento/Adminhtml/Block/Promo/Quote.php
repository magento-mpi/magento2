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
 * Catalog price rules
 *
 * @category    Magento
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Promo;

class Quote extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'promo_quote';
        $this->_headerText = __('Shopping Cart Price Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
        
    }
}
