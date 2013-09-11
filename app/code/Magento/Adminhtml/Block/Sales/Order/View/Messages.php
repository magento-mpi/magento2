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
 * Order view messages
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\View;

class Messages extends \Magento\Adminhtml\Block\Messages
{

    protected function _getOrder()
    {
        return \Mage::registry('sales_order');
    }

    public function _prepareLayout()
    {
        /**
         * Check customer existing
         */
        $customer = \Mage::getModel('Magento\Customer\Model\Customer')->load($this->_getOrder()->getCustomerId());

        /**
         * Check Item products existing
         */
        $productIds = array();
        foreach ($this->_getOrder()->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        return parent::_prepareLayout();
    }

}
