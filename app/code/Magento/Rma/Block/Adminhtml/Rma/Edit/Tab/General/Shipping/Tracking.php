<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping;

/**
 * Shipment tracking
 */
class Tracking extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData;

    /**
     * Shipping carrier factory
     *
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * Rma shipping collection
     *
     * @var \Magento\Rma\Model\Resource\Shipping\CollectionFactory
     */
    protected $_shippingCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Rma\Model\Resource\Shipping\CollectionFactory $shippingCollectionFactory
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Rma\Model\Resource\Shipping\CollectionFactory $shippingCollectionFactory,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_shippingCollectionFactory = $shippingCollectionFactory;
        $this->_carrierFactory = $carrierFactory;
        $this->_coreRegistry = $registry;
        $this->_rmaData = $rmaData;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getRma()
    {
        return $this->_coreRegistry->registry('current_rma');
    }

    /**
     * Gets available carriers
     *
     * @return array
     */
    public function getCarriers()
    {
        return $this->_rmaData->getAllowedShippingCarriers($this->getRma()->getStoreId());
    }

    /**
     * Gets all tracks
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getAllTracks()
    {
        return $this->_shippingCollectionFactory->create()->addFieldToFilter(
            'rma_entity_id',
            $this->getRma()->getId()
        )->addFieldToFilter(
            'is_admin',
            array("neq" => \Magento\Rma\Model\Shipping::IS_ADMIN_STATUS_ADMIN_LABEL)
        );
    }

    /**
     * Prepares layout of block
     *
     * @return \Magento\View\Element\AbstractBlock|void
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('shipment_tracking_info').parentNode, '" . $this->getSubmitUrl() . "')";
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                array('label' => __('Add'), 'class' => 'save', 'onclick' => $onclick)
            )
        );
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
     * Retrieve save url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('adminhtml/*/addTrack/', array('id' => $this->getRma()->getId()));
    }

    /**
     * Retrieve save button html
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve remove url
     *
     * @param \Magento\Rma\Model\Shipping $track
     * @return string
     */
    public function getRemoveUrl($track)
    {
        return $this->getUrl(
            'adminhtml/*/removeTrack/',
            array('id' => $this->getRma()->getId(), 'track_id' => $track->getId())
        );
    }

    /**
     * Get Carrier Title
     *
     * @param string $code
     * @return string
     */
    public function getCarrierTitle($code)
    {
        $carrier = $this->_carrierFactory->create($code);
        return $carrier ? $carrier->getConfigData('title') : __('Custom Value');
    }
}
