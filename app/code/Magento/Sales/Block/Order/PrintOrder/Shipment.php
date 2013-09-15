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
 * Sales order details block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Sales\Block\Order\PrintOrder;

class Shipment extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * Tracks for Shippings
     *
     * @var array
     */
    protected $_tracks = array();

     /**
     * Order shipments collection
     *
     * @var array|\Magento\Sales\Model\Resource\Order\Shipment\Collection
     */
    protected $_shipmentsCollection;

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

    /**
     * Load all tracks and save it to local cache by shipments
     *
     * @return \Magento\Sales\Block\Order\PrintOrder\Shipment
     */
    protected function _beforeToHtml()
    {
        $tracksCollection = $this->getOrder()->getTracksCollection();

        foreach ($tracksCollection->getItems() as $track) {
            $shipmentId = $track->getParentId();
            $this->_tracks[$shipmentId][] = $track;
        }

        $shipment = $this->_coreRegistry->registry('current_shipment');
        if ($shipment) {
            $this->_shipmentsCollection = array($shipment);
        } else {
            $this->_shipmentsCollection = $this->getOrder()->getShipmentsCollection();
        }

        return parent::_beforeToHtml();
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

    public function getBackUrl()
    {
        return \Mage::getUrl('*/*/history');
    }

    public function getPrintUrl()
    {
        return \Mage::getUrl('*/*/print');
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    public function getShipment()
    {
        return $this->_coreRegistry->registry('current_shipment');
    }

    protected function _prepareItem(\Magento\Core\Block\AbstractBlock $renderer)
    {
        $renderer->setPrintStatus(true);

        return parent::_prepareItem($renderer);
    }

     /**
     * Retrieve order shipments collection
     *
     * @return array|\Magento\Sales\Model\Resource\Order\Shipment\Collection
     */
    public function getShipmentsCollection()
    {
        return $this->_shipmentsCollection;
    }

    /**
     * Getter for order tracking numbers collection per shipment
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return array
     */
    public function getShipmentTracks($shipment)
    {
        $tracks = array();
        if (!empty($this->_tracks[$shipment->getId()])) {
            $tracks = $this->_tracks[$shipment->getId()];
        }
        return $tracks;
    }

    /**
     * Getter for shipment address by format
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return string
     */
    public function getShipmentAddressFormattedHtml($shipment)
    {
        $shippingAddress = $shipment->getShippingAddress();
        if(!($shippingAddress instanceof \Magento\Sales\Model\Order\Address)) {
            return '';
        }
        return $shippingAddress->format('html');
    }

    /**
     * Getter for billing address of order by format
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    public function getBillingAddressFormattedHtml($order)
    {
        $billingAddress = $order->getBillingAddress();
        if(!($billingAddress instanceof \Magento\Sales\Model\Order\Address)) {
            return '';
        }
        return $billingAddress->format('html');
    }

    /**
     * Getter for billing address of order by format
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return array
     */
    public function getShipmentItems($shipment)
    {
        $res = array();
        foreach ($shipment->getItemsCollection() as $item) {
            if (!$item->getOrderItem()->getParentItem()) {
                $res[] = $item;
            }
        }
        return $res;
    }
}

