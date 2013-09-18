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
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Order;

class Creditmemo extends \Magento\Sales\Block\Order\Creditmemo\Items
{

    protected $_template = 'order/creditmemo.phtml';

    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(__('Order # %1', $this->getOrder()->getRealOrderId()));
        }
        $this->setChild(
            'payment_info',
            $this->helper('Magento\Payment\Helper\Data')->getInfoBlock($this->getOrder()->getPayment())
        );
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            return \Mage::getUrl('*/*/history');
        }
        return \Mage::getUrl('*/*/form');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return string
     */
    public function getBackTitle()
    {
        if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            return __('Back to My Orders');
        }
        return __('View Another Order');
    }

    public function getInvoiceUrl($order)
    {
        return \Mage::getUrl('*/*/invoice', array('order_id' => $order->getId()));
    }

    public function getShipmentUrl($order)
    {
        return \Mage::getUrl('*/*/shipment', array('order_id' => $order->getId()));
    }

    public function getViewUrl($order)
    {
        return \Mage::getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    public function getPrintCreditmemoUrl($creditmemo){
        return \Mage::getUrl('*/*/printCreditmemo', array('creditmemo_id' => $creditmemo->getId()));
    }

    public function getPrintAllCreditmemosUrl($order){
        return \Mage::getUrl('*/*/printCreditmemo', array('order_id' => $order->getId()));
    }
}
