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
 * Sales order view items block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Order\Shipment;

class Items extends \Magento\Sales\Block\Items\AbstractItems
{
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
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    public function getPrintShipmentUrl($shipment){
        return \Mage::getUrl('*/*/printShipment', array('shipment_id' => $shipment->getId()));
    }

    public function getPrintAllShipmentsUrl($order){
        return \Mage::getUrl('*/*/printShipment', array('order_id' => $order->getId()));
    }

    /**
     * Get html of shipment comments block
     *
     * @param   \Magento\Sales\Model\Order\Shipment $shipment
     * @return  string
     */
    public function getCommentsHtml($shipment)
    {
        $html = '';
        $comments = $this->getChildBlock('shipment_comments');
        if ($comments) {
            $comments->setEntity($shipment)
                ->setTitle(__('About Your Shipment'));
            $html = $comments->toHtml();
        }
        return $html;
    }
}
