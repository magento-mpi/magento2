<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipping Method Block at RMA page
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General;

class Shippingmethod
    extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\AbstractGeneral
{

    /**
     * PSL Button statuses
     */
    const PSL_DISALLOWED    = 0;
    const PSL_ALLOWED       = 1;
    const PSL_DISABLED      = 2;

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
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_taxData = $taxData;
        $this->_rmaData = $rmaData;
        parent::__construct($coreData, $context, $registry, $data);
    }

    public function _construct()
    {
        $buttonStatus       = \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod::PSL_DISALLOWED;
        if ($this->_getShippingAvailability() && $this->getRma() && $this->getRma()->isAvailableForPrintLabel()) {
            $buttonStatus   = \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod::PSL_ALLOWED;
        } elseif($this->getRma() && $this->getRma()->getButtonDisabledStatus()) {
            $buttonStatus   = \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod::PSL_DISABLED;
        }

        $this->setIsPsl($buttonStatus);
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
     * Defines whether Shipping method settings allow to create shipping label
     *
     * @return bool
     */
    protected function _getShippingAvailability()
    {
        $carriers = array();
        if ($this->getRma()) {
            $carriers = $this->_rmaData->getAllowedShippingCarriers($this->getRma()->getStoreId());
        }
        return !empty($carriers);
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Rma\Model\Shipping
     */
    public function getShipment()
    {
        return \Mage::getModel('Magento\Rma\Model\Shipping')
            ->getShippingLabelByRma($this->getRma());
    }

    /**
     * Return price according to store
     *
     * @param  string $price
     * @return double
     */
    public function getShippingPrice($price)
    {
        return \Mage::app()
            ->getStore($this->getRma()->getStoreId())
            ->convertPrice(
                $this->_taxData->getShippingPrice(
                    $price
                ),
                true,
                false
            )
        ;
    }

    /**
     * Get packed products in packages
     *
     * @return array
     */
    public function getPackages()
    {
        $packages = $this->getShipment()->getPackages();
        if ($packages) {
            $packages = unserialize($packages);
        } else {
            $packages = array();
        }
        return $packages;
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
        $carrierCode= $this->getShipment()->getCarrierCode();
        if (!$carrierCode) {
            return false;
        }
        $address    = $order->getShippingAddress();
        $shipperAddressCountryCode  = $address->getCountryId();
        $recipientAddressCountryCode= $this->_rmaData->getReturnAddressModel($storeId)->getCountryId();

        if (($carrierCode == 'fedex' || $carrierCode == 'dhl')
            && $shipperAddressCountryCode != $recipientAddressCountryCode) {
            return true;
        }
        return false;
    }

    /**
     * Get print label button html
     *
     * @return string
     */
    public function getPrintLabelButton()
    {
        $data['id'] = $this->getRma()->getId();
        $url        = $this->getUrl('*/rma/printLabel', $data);

        return $this->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Widget\Button')
            ->setData(array(
                'label'   => __('Print Shipping Label'),
                'onclick' => 'setLocation(\'' . $url . '\')'
            ))
            ->toHtml();
    }

    /**
     * Show packages button html
     *
     * @return string
     */
    public function getShowPackagesButton()
    {
        return $this->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Widget\Button')
            ->setData(array(
                'label'   => __('Show Packages'),
                'onclick' => 'showPackedWindow();'
            ))
            ->toHtml();
    }

    /**
     * Print button for creating pdf
     *
     * @return string
     */
    public function getPrintButton()
    {
        $data['id'] = $this->getRma()->getId();
        $url        = $this->getUrl('*/rma/printPackage', $data);

        return $this->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Widget\Button')
            ->setData(array(
                'label'   => __('Print'),
                'onclick' => 'setLocation(\'' . $url . '\')'
            ))
            ->toHtml();
    }

    /**
     * Return name of container type by its code
     *
     * @param string $code
     * @return string
     */
    public function getContainerTypeByCode($code)
    {
        $carrierCode= $this->getShipment()->getCarrierCode();
        $carrier    = $this->_rmaData->getCarrier($carrierCode, $this->getRma()->getStoreId());
        if ($carrier) {
            $containerTypes = $carrier->getContainerTypes();
            $containerType = !empty($containerTypes[$code]) ? $containerTypes[$code] : '';
            return $containerType;
        }
        return '';
    }

    /**
     * Return name of delivery confirmation type by its code
     *
     * @param string $code
     * @return string
     */
    public function getDeliveryConfirmationTypeByCode($code)
    {
        $storeId    = $this->getRma()->getStoreId();
        $countryId  = $this->_rmaData->getReturnAddressModel($storeId)->getCountryId();
        $carrierCode= $this->getShipment()->getCarrierCode();
        $carrier    = $this->_rmaData->getCarrier($carrierCode, $this->getRma()->getStoreId());
        if ($carrier) {
            $params = new \Magento\Object(array('country_recipient' => $countryId));
            $confirmationTypes = $carrier->getDeliveryConfirmationTypes($params);
            $containerType = !empty($confirmationTypes[$code]) ? $confirmationTypes[$code] : '';
            return $containerType;
        }
        return '';
    }

    /**
     * Display formatted price
     *
     * @param float $price
     * @return string
     */
    public function displayPrice($price)
    {
        return $this->getRma()->getOrder()->formatPriceTxt($price);
    }

    /**
     * Get ordered qty of item
     *
     * @param int $itemId
     * @return int|null
     */
    public function getQtyOrderedItem($itemId)
    {
        if ($itemId) {
            return $this->getRma()->getOrder()->getItemById($itemId)->getQtyOrdered()*1;
        } else {
            return;
        }
    }

    /**
     * Return content types of package
     *
     * @return array
     */
    public function getContentTypes()
    {
        $order      = $this->getRma()->getOrder();
        $storeId    = $this->getRma()->getStoreId();
        $address    = $order->getShippingAddress();

        $carrierCode= $this->getShipment()->getCarrierCode();
        $carrier    = $this->_rmaData->getCarrier($carrierCode, $storeId);

        $countryShipper = $this->_storeConfig->getConfig(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_COUNTRY_ID, $storeId);
        if ($carrier) {
            $params = new \Magento\Object(array(
                'method'            => $carrier->getMethod(),
                'country_shipper'   => $countryShipper,
                'country_recipient' => $address->getCountryId(),
            ));
            return $carrier->getContentTypes($params);
        }
        return array();
    }

    /**
     * Return name of content type by its code
     *
     * @param string $code
     * @return string
     */
    public function getContentTypeByCode($code)
    {
        $contentTypes = $this->getContentTypes();
        if (!empty($contentTypes[$code])) {
            return $contentTypes[$code];
        }
        return '';
    }
}
