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
 * Adminhtml shipment packaging
 */

namespace Magento\Sales\Block\Adminhtml\Order\Shipment;

class Packaging extends \Magento\Adminhtml\Block\Template
{
    /**
     * @var \Magento\Usa\Model\Shipping\Carrier\Usps\Source\Size
     */
    protected $_sourceSizeModel;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Usa\Model\Shipping\Carrier\Usps\Source\Size $sourceSizeModel
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Usa\Model\Shipping\Carrier\Usps\Source\Size $sourceSizeModel,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_sourceSizeModel = $sourceSizeModel;
        parent::__construct($coreData, $context, $data);
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
     * Configuration for popup window for packaging
     *
     * @return string
     */
    public function getConfigDataJson()
    {
        $shipmentId = $this->getShipment()->getId();
        $orderId = $this->getRequest()->getParam('order_id');
        $urlParams = array();

        $itemsQty       = array();
        $itemsPrice     = array();
        $itemsName      = array();
        $itemsWeight    = array();
        $itemsProductId = array();

        if ($shipmentId) {
            $urlParams['shipment_id'] = $shipmentId;
            $createLabelUrl = $this->getUrl('sales/order_shipment/createLabel', $urlParams);
            $itemsGridUrl = $this->getUrl('sales/order_shipment/getShippingItemsGrid', $urlParams);
            foreach ($this->getShipment()->getAllItems() as $item) {
                $itemsQty[$item->getId()]           = $item->getQty();
                $itemsPrice[$item->getId()]         = $item->getPrice();
                $itemsName[$item->getId()]          = $item->getName();
                $itemsWeight[$item->getId()]        = $item->getWeight();
                $itemsProductId[$item->getId()]     = $item->getProductId();
                $itemsOrderItemId[$item->getId()]   = $item->getOrderItemId();
            }
        } else if ($orderId) {
            $urlParams['order_id'] = $orderId;
            $createLabelUrl = $this->getUrl('sales/order_shipment/save', $urlParams);
            $itemsGridUrl = $this->getUrl('sales/order_shipment/getShippingItemsGrid', $urlParams);

            foreach ($this->getShipment()->getAllItems() as $item) {
                $itemsQty[$item->getOrderItemId()]          = $item->getQty()*1;
                $itemsPrice[$item->getOrderItemId()]        = $item->getPrice();
                $itemsName[$item->getOrderItemId()]         = $item->getName();
                $itemsWeight[$item->getOrderItemId()]       = $item->getWeight();
                $itemsProductId[$item->getOrderItemId()]    = $item->getProductId();
                $itemsOrderItemId[$item->getOrderItemId()]  = $item->getOrderItemId();
            }
        }
        $data = array(
            'createLabelUrl'            => $createLabelUrl,
            'itemsGridUrl'              => $itemsGridUrl,
            'errorQtyOverLimit'         => __('You are trying to add a quantity for some products that doesn\'t match the quantity that was shipped.'),
            'titleDisabledSaveBtn'      => __('Products should be added to package(s)'),
            'validationErrorMsg'        => __('The value that you entered is not valid.'),
            'shipmentItemsQty'          => $itemsQty,
            'shipmentItemsPrice'        => $itemsPrice,
            'shipmentItemsName'         => $itemsName,
            'shipmentItemsWeight'       => $itemsWeight,
            'shipmentItemsProductId'    => $itemsProductId,
            'shipmentItemsOrderItemId'  => $itemsOrderItemId,
            'customizable'              => $this->_getCustomizableContainers(),
        );
        return $this->_coreData->jsonEncode($data);
    }

    /**
     * Return container types of carrier
     *
     * @return array
     */
    public function getContainers()
    {
        $order = $this->getShipment()->getOrder();
        $storeId = $this->getShipment()->getStoreId();
        $address = $order->getShippingAddress();
        $carrier = $order->getShippingCarrier();
        $countryShipper = $this->_storeConfig->getConfig(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_COUNTRY_ID, $storeId);
        if ($carrier) {
            $params = new \Magento\Object(array(
                'method' => $order->getShippingMethod(true)->getMethod(),
                'country_shipper' => $countryShipper,
                'country_recipient' => $address->getCountryId(),
            ));
            return $carrier->getContainerTypes($params);
        }
        return array();
    }

    /**
     * Get codes of customizable container types of carrier
     *
     * @return array
     */
    protected function _getCustomizableContainers()
    {
        $carrier = $this->getShipment()->getOrder()->getShippingCarrier();
        if ($carrier) {
            return $carrier->getCustomizableContainerTypes();
        }
        return array();
    }

    /**
     * Return name of container type by its code
     *
     * @param string $code
     * @return string
     */
    public function getContainerTypeByCode($code)
    {
        $carrier = $this->getShipment()->getOrder()->getShippingCarrier();
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
        $countryId = $this->getShipment()->getOrder()->getShippingAddress()->getCountryId();
        $carrier = $this->getShipment()->getOrder()->getShippingCarrier();
        if ($carrier) {
            $params = new \Magento\Object(array('country_recipient' => $countryId));
            $confirmationTypes = $carrier->getDeliveryConfirmationTypes($params);
            $confirmationType = !empty($confirmationTypes[$code]) ? $confirmationTypes[$code] : '';
            return $confirmationType;
        }
        return '';
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
     * Get item of shipment by its id
     *
     * @param  $itemId
     * @param  $itemsOf
     * @return \Magento\Object
     */
    public function getShipmentItem($itemId, $itemsOf)
    {
        $items = $this->getShipment()->getAllItems();
        foreach ($items as $item) {
            if ($itemsOf == 'order' && $item->getOrderItemId() == $itemId) {
                return $item;
            } else if ($itemsOf == 'shipment' && $item->getId() == $itemId) {
                return $item;
            }
        }
        return new \Magento\Object();
    }

    /**
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $storeId = $this->getShipment()->getStoreId();
        $order = $this->getShipment()->getOrder();
        $address = $order->getShippingAddress();
        $shipperAddressCountryCode = $this->_storeConfig->getConfig(
            \Magento\Shipping\Model\Shipping::XML_PATH_STORE_COUNTRY_ID,
            $storeId
        );
        $recipientAddressCountryCode = $address->getCountryId();
        if ($shipperAddressCountryCode != $recipientAddressCountryCode) {
            return true;
        }
        return false;
    }

    /**
     * Return delivery confirmation types of current carrier
     *
     * @return array
     */
    public function getDeliveryConfirmationTypes()
    {
        $countryId = $this->getShipment()->getOrder()->getShippingAddress()->getCountryId();
        $carrier = $this->getShipment()->getOrder()->getShippingCarrier();
        $params = new \Magento\Object(array('country_recipient' => $countryId));
        if ($carrier && is_array($carrier->getDeliveryConfirmationTypes($params))) {
            return $carrier->getDeliveryConfirmationTypes($params);
        }
        return array();
    }

    /**
     * Print button for creating pdf
     *
     * @return string
     */
    public function getPrintButton()
    {
        $data['shipment_id'] = $this->getShipment()->getId();
        $url = $this->getUrl('sales/order_shipment/printPackage', $data);
        return $this->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Widget\Button')
            ->setData(array(
                'label'   => __('Print'),
                'onclick' => 'setLocation(\'' . $url . '\')'
            ))
            ->toHtml();
    }

    /**
     * Check whether girth is allowed for current carrier
     *
     * @return void
     */
    public function isGirthAllowed()
    {
        return $this
            ->getShipment()
            ->getOrder()
            ->getShippingCarrier()
            ->isGirthAllowed($this->getShipment()->getOrder()->getShippingAddress()->getCountryId());
    }

    /**
     * Return content types of package
     *
     * @return array
     */
    public function getContentTypes()
    {
        $order = $this->getShipment()->getOrder();
        $storeId = $this->getShipment()->getStoreId();
        $address = $order->getShippingAddress();
        $carrier = $order->getShippingCarrier();
        $countryShipper = $this->_storeConfig->getConfig(\Magento\Shipping\Model\Shipping::XML_PATH_STORE_COUNTRY_ID, $storeId);
        if ($carrier) {
            $params = new \Magento\Object(array(
                'method' => $order->getShippingMethod(true)->getMethod(),
                'country_shipper' => $countryShipper,
                'country_recipient' => $address->getCountryId(),
            ));
            return $carrier->getContentTypes($params);
        }
        return array();
    }

    /**
     * Get Currency Code for Custom Value
     *
     * @return string
     */
    public function getCustomValueCurrencyCode()
    {
        $orderInfo = $this->getShipment()->getOrder();
        return $orderInfo->getBaseCurrency()->getCurrencyCode();
    }

    /**
     * Display formatted price
     *
     * @param float $price
     * @return string
     */
    public function displayPrice($price)
    {
        return $this->getShipment()->getOrder()->formatPriceTxt($price);
    }

    /**
     * Display formatted customs price
     *
     * @param float $price
     * @return string
     */
    public function displayCustomsPrice($price)
    {
        $orderInfo = $this->getShipment()->getOrder();
        return $orderInfo->getBaseCurrency()->formatTxt($price);
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
            return $this->getShipment()->getOrder()->getItemById($itemId)->getQtyOrdered()*1;
        } else {
            return;
        }
    }

    /**
     * Get Usps source size model
     *
     * @return \Magento\Usa\Model\Shipping\Carrier\Usps\Source\Size
     */
    public function getSourceSizeModel()
    {
        return $this->_sourceSizeModel;
    }
}
