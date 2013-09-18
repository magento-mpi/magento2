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
 * Shipment tracking control form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Invoice\Create;

class Tracking extends \Magento\Adminhtml\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    public function _construct()
    {
        $this->setTemplate('sales/order/invoice/create/tracking.phtml');
    }

    /**
     * Prepares layout of block
     *
     * @return \Magento\Adminhtml\Block\Sales\Order\View\Giftmessage
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label'   => __('Add Tracking Number'),
            'class'   => '',
            'onclick' => 'trackingControl.add()'
        ));
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        return $this->_coreRegistry->registry('current_shipment');
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getInvoice()
    {
        return $this->_coreRegistry->registry('current_invoice');
    }

    /**
     * Retrieve
     *
     * @return unknown
     */
    public function getCarriers()
    {

        $carriers = array();
        $carrierInstances = \Mage::getSingleton('Magento\Shipping\Model\Config')->getAllCarriers(
            $this->getInvoice()->getStoreId()
        );
        $carriers['custom'] = __('Custom Value');
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }
        return $carriers;
    }
}
