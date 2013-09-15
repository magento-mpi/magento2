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
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping;

class Packaging extends \Magento\Backend\Block\Template
{
    /**
     * Variable to store RMA instance
     *
     * @var null|\Magento\Rma\Model\Rma
     */
    protected $_rma = null;

    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * Usa data
     *
     * @var \Magento\Usa\Helper\Data
     */
    protected $_usaData = null;
    
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Usa\Helper\Data $usaData
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Usa\Helper\Data $usaData,
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_usaData = $usaData;
        $this->_rmaData = $rmaData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Declare rma instance
     *
     * @return  \Magento\Rma\Model\Item
     */
    public function getRma()
    {
        if (is_null($this->_rma)) {
            $this->_rma = $this->_coreRegistry->registry('current_rma');
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
            $params = new \Magento\Object(array(
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
            $params = new \Magento\Object(array('country_recipient' => $countryId));

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

            $order              = \Mage::getModel('Magento\Sales\Model\Order')->load($this->getRma()->getOrderId());
            $shipperAddress     = $order->getShippingAddress();
             if ($carrier) {
                $params = new \Magento\Object(array(
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
        return \Mage::getModel('Magento\Usa\Model\Shipping\Carrier\Usps\Source\Size')->toOptionArray();
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
            if ($regular[0]['value'] == \Magento\Usa\Model\Shipping\Carrier\Usps::SIZE_LARGE
                && in_array(
                    key($this->getContainers()),
                    array(
                        \Magento\Usa\Model\Shipping\Carrier\Usps::CONTAINER_NONRECTANGULAR,
                        \Magento\Usa\Model\Shipping\Carrier\Usps::CONTAINER_VARIABLE,
                    )
                )
            ) {
                $girthEnabled = true;
            }

            if (in_array(
                key($this->getContainers()),
                array(
                    \Magento\Usa\Model\Shipping\Carrier\Usps::CONTAINER_NONRECTANGULAR,
                    \Magento\Usa\Model\Shipping\Carrier\Usps::CONTAINER_RECTANGULAR,
                    \Magento\Usa\Model\Shipping\Carrier\Usps::CONTAINER_VARIABLE,
                )
            )) {
                $sizeEnabled = true;
            }
        }

        return array($girthEnabled, $sizeEnabled);
    }
}
