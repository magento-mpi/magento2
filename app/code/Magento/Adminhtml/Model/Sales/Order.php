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
 * Order control model
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Model\Sales;

class Order
{
    /**
     * Retrieve adminhtml session singleton
     *
     * @return \Magento\Adminhtml\Model\Session
     */
    protected function _getSession()
    {
        return \Mage::getSingleton('Magento\Adminhtml\Model\Session');
    }

    public function checkRelation(\Magento\Sales\Model\Order $order)
    {
        /**
         * Check customer existing
         */
        $customer = \Mage::getModel('Magento\Customer\Model\Customer')->load($order->getCustomerId());
        if (!$customer->getId()) {
            $this->_getSession()->addNotice(
                __(' The customer does not exist in the system anymore.')
            );
        }

        /**
         * Check Item products existing
         */
        $productIds = array();
        foreach ($order->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        $productCollection = \Mage::getModel('Magento\Catalog\Model\Product')->getCollection()
            ->addIdFilter($productIds)
            ->load();

        $hasBadItems = false;
        foreach ($order->getAllItems() as $item) {
            if (!$productCollection->getItemById($item->getProductId())) {
                $this->_getSession()->addError(
                   __('The item %1 (SKU %2) does not exist in the catalog anymore.', $item->getName(), $item->getSku()
                ));
                $hasBadItems = true;
            }
        }
        if ($hasBadItems) {
            $this->_getSession()->addError(
                __('Some items in this order are no longer in our catalog.')
            );
        }
        return $this;
    }

}
