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
 * Shipment tracking
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping;

class Tracking extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;
    
    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData;

    /**
     * @var \Magento\Rma\Model\Resource\Shipping\CollectionFactory
     */
    protected $_shippingFactory;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $_shippingConfig;

    /**
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Rma\Model\Resource\Shipping\CollectionFactory $shippingFactory
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Rma\Model\Resource\Shipping\CollectionFactory $shippingFactory,
        \Magento\Shipping\Model\Config $shippingConfig,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_rmaData = $rmaData;
        $this->_shippingFactory = $shippingFactory;
        $this->_shippingConfig = $shippingConfig;
        parent::__construct($coreData, $context, $data);
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
        /** @var $collection \Magento\Rma\Model\Resource\Shipping\Collection */
        $collection = $this->_shippingFactory->create();
        $collection->addFieldToFilter('rma_entity_id', $this->getRma()->getId());
        $collection->addFieldToFilter('is_admin', array(
            'neq' => \Magento\Rma\Model\Shipping::IS_ADMIN_STATUS_ADMIN_LABEL
        ));
        return $collection;
    }

    /**
     * Prepares layout of block
     *
     * @return string
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('shipment_tracking_info').parentNode, '".$this->getSubmitUrl()."')";
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')
                ->setData(
                    array(
                        'label'   => __('Add'),
                        'class'   => 'save',
                        'onclick' => $onclick
                    )
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
        return $this->getUrl('*/*/addTrack/', array('id' => $this->getRma()->getId()));
    }

    /**
     * Retrive save button html
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
        return $this->getUrl('*/*/removeTrack/', array(
            'id' => $this->getRma()->getId(),
            'track_id' => $track->getId()
        ));
    }

    /**
     * Get Carrier Title
     *
     * @param string $code
     * @return string
     */
    public function getCarrierTitle($code)
    {
        $carrier = $this->_shippingConfig->getCarrierInstance($code);
        return $carrier ? $carrier->getConfigData('title') : __('Custom Value');
    }
}
