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
 * Shipment packaging
 *
 * @category    Enterprise
 * @package     Enterprise_RMA
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shipping_Packaging extends Magento_Backend_Block_Template
{
    /**
     * Variable to store RMA instance
     *
     * @var null|Enterprise_Rma_Model_Rma
     */
    protected $_rma = null;

    /**
     * Rma data
     *
     * @var Enterprise_Rma_Helper_Data
     */
    protected $_rmaData = null;

    /**
     * Usa data
     *
     * @var Magento_Usa_Helper_Data
     */
    protected $_usaData = null;

    /**
     * @param Magento_Usa_Helper_Data $usaData
     * @param Enterprise_Rma_Helper_Data $rmaData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Usa_Helper_Data $usaData,
        Enterprise_Rma_Helper_Data $rmaData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_usaData = $usaData;
        $this->_rmaData = $rmaData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Declare rma instance
     *
     * @return  Enterprise_Rma_Model_Item
     */
    public function getRma()
    {
        if (is_null($this->_rma)) {
            $this->_rma = Mage::registry('current_rma');
        }
        return $this->_rma;
    }

    /**
     * Retrieve carrier
     *
     * @return string
     */
    public function getCarrier()
    {
        return $this->_rmaData->getCarrier(
            $this->getRequest()->getParam('method'),
            $this->getRma()->getStoreId()
        );
    }

    /**
     * Retrieve carrier method
     *
     * @return null|string
     */
    public function getCarrierMethod()
    {
        $code = explode('_', $this->getRequest()->getParam('method'), 2);

        if (is_array($code) && isset($code[1])) {
            return $code[1];
        } else {
            return null;
        }
    }

    /**
     * Return container types of carrier
     *
     * @return array
     */
    public function getContainers()
    {
        $order      = $this->getRma()->getOrder();
        $storeId    = $this->getRma()->getStoreId();
        $address    = $order->getShippingAddress();
        $carrier    = $this->getCarrier();

        $countryRecipient = $this->_rmaData->getReturnAddressModel($storeId)->getCountryId();
        if ($carrier) {
            $params = new Magento_Object(array(
                'method' => $this->getCarrierMethod(),
                'country_shipper' => $address->getCountryId(),
                'country_recipient' => $countryRecipient,
            ));
            return $carrier->getContainerTypes($params);
        }
        return array();
    }

    /**
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $storeId    = $this->getRma()->getStoreId();
        $order      = $this->getRma()->getOrder();
        $address                        = $order->getShippingAddress();
        $shipperAddressCountryCode      = $address->getCountryId();
        $recipientAddressCountryCode    = $this->_rmaData
            ->getReturnAddressModel($storeId)->getCountryId();

        if ($shipperAddressCountryCode != $recipientAddressCountryCode) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return delivery confirmation types of current carrier
     *
     * @return array
     */
    public function getDeliveryConfirmationTypes()
    {
        $storeId    = $this->getRma()->getStoreId();
        $code       = $this->getRequest()->getParam('method');
        if (!empty($code)) {
            list($carrierCode, $methodCode) = explode('_', $code, 2);
            $carrier    = $this->_rmaData->getCarrier($carrierCode, $storeId);
            $countryId  = $this->_rmaData->getReturnAddressModel($storeId)->getCountryId();
            $params = new Magento_Object(array('country_recipient' => $countryId));

            if ($carrier && is_array($carrier->getDeliveryConfirmationTypes($params))) {
                return $carrier->getDeliveryConfirmationTypes($params);
            }
        }
        return array();
    }

    /**
     * Check whether girth is allowed for current carrier
     *
     * @return bool
     */
    public function isGirthAllowed()
    {
        $storeId    = $this->getRma()->getStoreId();
        $code       = $this->getRequest()->getParam('method');
        $girth      = false;
        if (!empty($code)) {
            list($carrierCode, $methodCode) = explode('_', $code, 2);
            $carrier    = $this->_rmaData->getCarrier($carrierCode, $storeId);
            $countryId  = $this->_rmaData->getReturnAddressModel($storeId)->getCountryId();

            $girth = $carrier->isGirthAllowed($countryId);
        }
        return $girth;
    }

    /**
     * Return girth status
     *
     * @return bool
     */
    public function isGirthEnabled()
    {
        $code       = $this->getRequest()->getParam('method');
        $girth      = false;
        if (!empty($code)) {
            $girth = ($this->_usaData->displayGirthValue($code) && $this->isGirthAllowed()) ? 1 : 0;
        }

        return $girth;
    }

    /**
     * Return content types of package
     *
     * @return array
     */
    public function getContentTypes()
    {
        $storeId    = $this->getRma()->getStoreId();
        $code       = $this->getRequest()->getParam('method');
        if (!empty($code)) {
            list($carrierCode, $methodCode) = explode('_', $code, 2);
            $carrier    = $this->_rmaData->getCarrier($carrierCode, $storeId);
            $countryId  = $this->_rmaData->getReturnAddressModel($storeId)->getCountryId();

            $order              = Mage::getModel('Magento_Sales_Model_Order')->load($this->getRma()->getOrderId());
            $shipperAddress     = $order->getShippingAddress();
             if ($carrier) {
                $params = new Magento_Object(array(
                    'method'            => $methodCode,
                    'country_shipper'   => $shipperAddress->getCountryId(),
                    'country_recipient' => $countryId,
                ));
                return $carrier->getContentTypes($params);
            }
        }

        return array();
    }

    /**
     * Return customizable containers status
     *
     * @return bool
     */
    public function getCustomizableContainersStatus()
    {
        $storeId = $this->getRma()->getStoreId();
        $code    = $this->getRequest()->getParam('method');
        $carrier = $this->_rmaData->getCarrier($code, $storeId);
        if ($carrier) {
            $getCustomizableContainers =  $carrier->getCustomizableContainerTypes();

            if (in_array(key($this->getContainers()),$getCustomizableContainers)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return shipping carrier usps source sizes
     *
     * @return array
     */
    public function getShippingCarrierUspsSourceSize()
    {
        return Mage::getModel('Magento_Usa_Model_Shipping_Carrier_Usps_Source_Size')->toOptionArray();
    }

    /**
     * Check size and girth parameter
     *
     * @return array
     */
    public function checkSizeAndGirthParameter()
    {
        $storeId = $this->getRma()->getStoreId();
        $code    = $this->getRequest()->getParam('method');
        $carrier = $this->_rmaData->getCarrier($code, $storeId);

        $girthEnabled   = false;
        $sizeEnabled    = false;
        $regular        = $this->getShippingCarrierUspsSourceSize();
        if ($carrier && isset($regular[0]['value'])) {
            if ($regular[0]['value'] == Magento_Usa_Model_Shipping_Carrier_Usps::SIZE_LARGE
                && in_array(
                    key($this->getContainers()),
                    array(
                        Magento_Usa_Model_Shipping_Carrier_Usps::CONTAINER_NONRECTANGULAR,
                        Magento_Usa_Model_Shipping_Carrier_Usps::CONTAINER_VARIABLE,
                    )
                )
            ) {
                $girthEnabled = true;
            }

            if (in_array(
                key($this->getContainers()),
                array(
                    Magento_Usa_Model_Shipping_Carrier_Usps::CONTAINER_NONRECTANGULAR,
                    Magento_Usa_Model_Shipping_Carrier_Usps::CONTAINER_RECTANGULAR,
                    Magento_Usa_Model_Shipping_Carrier_Usps::CONTAINER_VARIABLE,
                )
            )) {
                $sizeEnabled = true;
            }
        }

        return array($girthEnabled, $sizeEnabled);
    }
}
