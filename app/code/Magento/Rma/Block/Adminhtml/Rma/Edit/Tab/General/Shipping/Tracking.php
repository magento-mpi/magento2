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
class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shipping_Tracking extends Magento_Backend_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;
    
    /**
     * Rma data
     *
     * @var Magento_Rma_Helper_Data
     */
    protected $_rmaData;

    /**
     * @var Magento_Rma_Model_Resource_Shipping_CollectionFactory
     */
    protected $_shippingFactory;

    /**
     * @var Magento_Shipping_Model_Config
     */
    protected $_shippingConfig;

    /**
     * @param Magento_Rma_Helper_Data $rmaData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Rma_Model_Resource_Shipping_CollectionFactory $shippingFactory
     * @param Magento_Shipping_Model_Config $shippingConfig
     * @param array $data
     */
    public function __construct(
        Magento_Rma_Helper_Data $rmaData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Rma_Model_Resource_Shipping_CollectionFactory $shippingFactory,
        Magento_Shipping_Model_Config $shippingConfig,
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
     * @return Magento_Sales_Model_Order_Shipment
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
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function getAllTracks()
    {
        /** @var $collection Magento_Rma_Model_Resource_Shipping_Collection */
        $collection = $this->_shippingFactory->create();
        $collection->addFieldToFilter('rma_entity_id', $this->getRma()->getId());
        $collection->addFieldToFilter('is_admin', array(
            'neq' => Magento_Rma_Model_Shipping::IS_ADMIN_STATUS_ADMIN_LABEL
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
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
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
     * @return Magento_Sales_Model_Order_Shipment
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
     * @param Magento_Rma_Model_Shipping $track
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
