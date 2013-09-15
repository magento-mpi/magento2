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

class Shipment extends \Magento\Core\Block\Template
{

    protected $_template = 'order/shipment.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

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

    public function getViewUrl($order)
    {
        return \Mage::getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    public function getCreditmemoUrl($order)
    {
        return \Mage::getUrl('*/*/creditmemo', array('order_id' => $order->getId()));
    }


    public function getPrintShipmentUrl($shipment){
        return \Mage::getUrl('*/*/printShipment', array('shipment_id' => $shipment->getId()));
    }

    public function getPrintAllShipmentsUrl($order){
        return \Mage::getUrl('*/*/printShipment', array('order_id' => $order->getId()));
    }
}
