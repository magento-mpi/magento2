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
 * Sales order view block
 *
 * @method int|null getCustomerId()
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Reorder;

class Sidebar extends \Magento\Core\Block\Template
{
    protected $_template = 'order/history.phtml';

    /**
     * Init orders
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->_getCustomerSession()->isLoggedIn()) {

            $this->initOrders();
        }

    }

    /**
     * Init customer order for display on front
     */
    public function initOrders()
    {
        $customerId = $this->getCustomerId() ? $this->getCustomerId()
            : $this->_getCustomerSession()->getCustomer()->getId();

        $orders = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Collection')
            ->addAttributeToFilter('customer_id', $customerId)
            ->addAttributeToFilter('state',
                array('in' => \Mage::getSingleton('Magento\Sales\Model\Order\Config')->getVisibleOnFrontStates())
            )
            ->addAttributeToSort('created_at', 'desc')
            ->setPage(1,1);
        //TODO: add filter by current website

        $this->setOrders($orders);
    }

    /**
     * Get list of last ordered products
     *
     * @return array
     */
    public function getItems()
    {
        $items = array();
        $order = $this->getLastOrder();
        $limit = 5;

        if ($order) {
            $website = \Mage::app()->getStore()->getWebsiteId();
            foreach ($order->getParentItemsRandomCollection($limit) as $item) {
                if ($item->getProduct() && in_array($website, $item->getProduct()->getWebsiteIds())) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * Check item product availability for reorder
     *
     * @param  \Magento\Sales\Model\Order\Item $orderItem
     * @return boolean
     */
    public function isItemAvailableForReorder(\Magento\Sales\Model\Order\Item $orderItem)
    {
        if ($orderItem->getProduct()) {
            return $orderItem->getProduct()->getStockItem()->getIsInStock();
        }
        return false;
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('checkout/cart/addgroup', array('_secure' => true));
    }

    /**
     * Last order getter
     *
     * @return \Magento\Sales\Model\Order|false
     */
    public function getLastOrder()
    {
        foreach ($this->getOrders() as $order) {
            return $order;
        }
        return false;
    }

    /**
     * Render "My Orders" sidebar block
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_getCustomerSession()->isLoggedIn() || $this->getCustomerId() ? parent::_toHtml() : '';
    }

    /**
     * Retrieve customer session instance
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getCustomerSession()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session');
    }
}
