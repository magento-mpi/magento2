<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create select store block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create\Store;

class Select extends \Magento\Backend\Block\Store\Switcher
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sc_store_select');
    }
}
