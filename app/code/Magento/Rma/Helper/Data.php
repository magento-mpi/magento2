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
 * RMA Helper
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Variable to contain country model
     *
     * @var Magento_Directory_Model_Country
     */
    protected $_countryModel = null;

    /**
     * Variable to contain order items collection for RMA creating
     *
     * @var Magento_Sales_Model_Resource_Order_Item_Collection
     */
    protected $_orderItems = null;

    /**
     * Allowed hash keys for shipment tracking
     *
     * @var array
     */
    protected $_allowedHashKeys = array('rma_id', 'track_id');

    /**
     * Application model
     *
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * Store config model
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * Country factory
     *
     * @var Magento_Directory_Model_CountryFactory
     */
    protected $_countryFactory;

    /**
     * Region factory
     *
     * @var Magento_Directory_Model_RegionFactory
     */
    protected $_regionFactory;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Rma_Model_Resource_ItemFactory
     */
    protected $_rmaItemFactory;

    /**
     * @param Magento_Rma_Model_Resource_ItemFactory $rmaItemFactory
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Directory_Model_CountryFactory $countryFactory
     * @param Magento_Directory_Model_RegionFactory $regionFactory
     */
    public function __construct(
        Magento_Rma_Model_Resource_ItemFactory $rmaItemFactory,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_App $app,
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Directory_Model_CountryFactory $countryFactory,
        Magento_Directory_Model_RegionFactory $regionFactory
    ) {
        $this->_rmaItemFactory = $rmaItemFactory;
        $this->_authSession = $authSession;
        $this->_customerSession = $customerSession;
        $this->_coreData = $coreData;
        $this->_app = $app;
        $this->_storeConfig = $storeConfig;
        $this->_countryFactory = $countryFactory;
        $this->_regionFactory = $regionFactory;
        parent::__construct($context);
    }

    /**
     * Checks whether RMA module is enabled for frontend in system config
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_storeConfig->getConfigFlag(Magento_Rma_Model_Rma::XML_PATH_ENABLED);
    }

    /**
     * Checks for ability to create RMA
     *
     * @param  int|Magento_Sales_Model_Order $order
     * @param  bool $forceCreate - set yes when you don't need to check config setting (for admin side)
     * @return bool
     */
    public function canCreateRma($order, $forceCreate = false)
    {
        $items = $this->getOrderItems($order);
        if ($items->count() && ($forceCreate || $this->isEnabled())) {
            return true;
        }

        return false;
    }

    /**
     * Gets available order items collection for RMA creating
     *
     * @param  int|Magento_Sales_Model_Order $orderId
     * @param  bool $onlyParents If needs only parent items (only for backend)
     * @throws Magento_Core_Exception
     * @return Magento_Sales_Model_Resource_Order_Item_Collection
     */
    public function getOrderItems($orderId, $onlyParents = false)
    {
        if ($orderId instanceof Magento_Sales_Model_Order) {
            $orderId = $orderId->getId();
        }
        if (!is_numeric($orderId)) {
            Mage::throwException(__('This is not a valid order.'));
        }
        if (is_null($this->_orderItems) || !isset($this->_orderItems[$orderId])) {
            $this->_orderItems[$orderId] = $this->_rmaItemFactory
                ->create()
                ->getOrderItems($orderId);
        }

        if ($onlyParents) {
            foreach ($this->_orderItems[$orderId] as &$item) {
                if ($item->getParentItemId()) {
                    $this->_orderItems[$orderId]->removeItemByKey($item->getId());
                }
                if ($item->getProductType() == Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                    $productOptions = $item->getProductOptions();
                    $item->setName($productOptions['simple_name']);
                }
            }
        }

        return $this->_orderItems[$orderId];
    }

    /**
     * Get url for rma create
     *
     * @param  Magento_Sales_Model_Order $order
     * @return string
     */
    public function getReturnCreateUrl($order)
    {
        if ($this->_customerSession->isLoggedIn()) {
            return Mage::getUrl('rma/return/create', array('order_id' => $order->getId()));
        } else {
            return Mage::getUrl('rma/guest/create', array('order_id' => $order->getId()));
        }
    }

    /**
     * Get formatted return address
     *
     * @param string $formatCode
     * @param array $data - array of address data
     * @param int|null $storeId - Store Id
     * @return string
     */
    public function getReturnAddress($formatCode = 'html', $data = array(), $storeId = null)
    {
        if (empty($data)) {
            $data = $this->getReturnAddressData($storeId);
        }

        $format = null;

        if (isset($data['countryId'])) {
            $countryModel = $this->_getCountryModel()->load($data['countryId']);
            $format = $countryModel->getFormat($formatCode);
        }

        if (!$format) {
            $path = sprintf('%s%s', Magento_Customer_Model_Address_Config::XML_PATH_ADDRESS_TEMPLATE, $formatCode);
            $format = $this->_storeConfig->getConfig($path, $storeId);
        }

        $formater = new Magento_Filter_Template();
        $formater->setVariables($data);
        return $formater->filter($format);
    }

    /**
     * Get return contact name
     *
     * @param int|null $storeId
     * @return Magento_Object
     */
    public function getReturnContactName($storeId = null)
    {
        $contactName = new Magento_Object();
        if ($this->_storeConfig->getConfigFlag(Magento_Rma_Model_Rma::XML_PATH_USE_STORE_ADDRESS, $storeId)) {
            $admin = $this->_authSession->getUser();
            $contactName->setFirstName($admin->getFirstname());
            $contactName->setLastName($admin->getLastname());
            $contactName->setName($admin->getName());
        } else {
            $name = $this->_storeConfig->getConfig(Magento_Rma_Model_Shipping::XML_PATH_CONTACT_NAME, $storeId);
            $contactName->setFirstName('');
            $contactName->setLastName($name);
            $contactName->setName($name);
        }
        return $contactName;
    }

    /**
     * Get return address model
     *
     * @param int|null $storeId
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getReturnAddressModel($storeId = null)
    {
        $addressModel = Mage::getModel('Magento_Sales_Model_Quote_Address');
        $addressModel->setData($this->getReturnAddressData($storeId));
        $addressModel->setCountryId($addressModel->getData('countryId'));
        $addressModel->setStreet($addressModel->getData('street1')."\n".$addressModel->getData('street2'));

        return $addressModel;
    }

    /**
     * Get return address array depending on config settings
     *
     * @param Magento_Core_Model_Store|null|int $store
     * @return array
     */
    public function getReturnAddressData($store = null)
    {
        if (!$store) {
            $store = $this->_app->getStore();
        }

        if ($this->_storeConfig->getConfigFlag(Magento_Rma_Model_Rma::XML_PATH_USE_STORE_ADDRESS, $store)) {
            $data = array(
                'city' => $this->_storeConfig
                    ->getConfig(Magento_Shipping_Model_Shipping::XML_PATH_STORE_CITY, $store),
                'countryId' => $this->_storeConfig
                    ->getConfig(Magento_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID, $store),
                'postcode' => $this->_storeConfig
                    ->getConfig(Magento_Shipping_Model_Shipping::XML_PATH_STORE_ZIP, $store),
                'region_id' => $this->_storeConfig
                    ->getConfig(Magento_Shipping_Model_Shipping::XML_PATH_STORE_REGION_ID, $store),
                'street2' => $this->_storeConfig
                    ->getConfig(Magento_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS2, $store),
                'street1' => $this->_storeConfig
                    ->getConfig(Magento_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS1, $store),
            );
        } else {
            $data = array(
                'city' => $this->_storeConfig
                    ->getConfig(Magento_Rma_Model_Shipping::XML_PATH_CITY, $store),
                'countryId' => $this->_storeConfig
                    ->getConfig(Magento_Rma_Model_Shipping::XML_PATH_COUNTRY_ID, $store),
                'postcode' => $this->_storeConfig
                    ->getConfig(Magento_Rma_Model_Shipping::XML_PATH_ZIP, $store),
                'region_id' => $this->_storeConfig
                    ->getConfig(Magento_Rma_Model_Shipping::XML_PATH_REGION_ID, $store),
                'street2' => $this->_storeConfig
                    ->getConfig(Magento_Rma_Model_Shipping::XML_PATH_ADDRESS2, $store),
                'street1' => $this->_storeConfig
                    ->getConfig(Magento_Rma_Model_Shipping::XML_PATH_ADDRESS1, $store),
            );
        }

        $data['country'] = (!empty($data['countryId']))
            ? $this->_countryFactory->create()->loadByCode($data['countryId'])->getName()
            : '';
        $region = $this->_regionFactory->create()->load($data['region_id']);
        $data['region_id'] = $region->getCode();
        $data['region'] = $region->getName();
        $data['company'] = $this->_storeConfig->getConfig(Magento_Core_Model_Store::XML_PATH_STORE_STORE_NAME, $store);
        $data['telephone']  = $this->_storeConfig->getConfig(Magento_Core_Model_Store::XML_PATH_STORE_STORE_PHONE, $store);

        return $data;
    }

    /**
     * Get Country model
     *
     * @return Magento_Directory_Model_Country
     */
    protected function _getCountryModel()
    {
        if (is_null($this->_countryModel)) {
            $this->_countryModel = Mage::getModel('Magento_Directory_Model_Country');
        }
        return $this->_countryModel;
    }

    /**
     * Get Contact Email Address title
     *
     * @return string
     */
    public function getContactEmailLabel()
    {
        return __('Contact Email Address');
    }

    /**
     * Get key=>value array of "big four" shipping carriers with store-defined labels
     *
     * @param int|Magento_Core_Model_Store|null $store
     * @return array
     */
    public function getShippingCarriers($store = null)
    {
        $return = array();
        foreach (array('dhl', 'fedex', 'ups', 'usps') as $carrier) {
            $return[$carrier] = $this->_storeConfig->getConfig('carriers/' . $carrier . '/title', $store);
        }
        return $return;
    }

    /**
     * Get key=>value array of enabled in website and enabled for RMA shipping carriers
     * from "big four" with their store-defined labels
     *
     * @param int|Magento_Core_Model_Store|null $store
     * @return array
     */
    public function getAllowedShippingCarriers($store = null)
    {
        $return = $this->getShippingCarriers($store);
        foreach (array_keys($return) as $carrier) {
            if (!$this->_storeConfig->getConfig('carriers/' . $carrier . '/active_rma', $store)) {
                unset ($return[$carrier]);
            }
        }
        return $return;
    }

    /**
     * Retrieve carrier
     *
     * @param string $code Shipping method code
     * @param mixed $storeId
     * @return false|Magento_Usa_Model_Shipping_Carrier_Abstract
     */
    public function getCarrier($code, $storeId = null)
    {
        $data           = explode('_', $code, 2);
        $carrierCode    = $data[0];

        if (!$this->_storeConfig->getConfig('carriers/' . $carrierCode . '/active_rma', $storeId)) {
            return false;
        }
        $className = $this->_storeConfig->getConfig('carriers/'.$carrierCode.'/model', $storeId);
        if (!$className) {
            return false;
        }
        $obj = Mage::getModel($className);
        if ($storeId) {
            $obj->setStore($storeId);
        }
        return $obj;
    }

    /**
     * Shipping package popup URL getter
     *
     * @param $model Magento_Rma_Model_Rma
     * @param $action string
     * @return string
     */
    public function getPackagePopupUrlByRmaModel($model, $action = 'package')
    {
        $key    = 'rma_id';
        $method = 'getId';
        $param = array(
             'hash' => $this->_coreData->urlEncode("{$key}:{$model->$method()}:{$model->getProtectCode()}")
        );

         $storeId = is_object($model) ? $model->getStoreId() : null;
         $storeModel = Mage::app()->getStore($storeId);
         return $storeModel->getUrl('rma/tracking/'.$action, $param);
    }

    /**
     * Shipping tracking popup URL getter
     *
     * @param $track
     * @return string
     */
    public function getTrackingPopupUrlBySalesModel($track)
    {
        if ($track instanceof Magento_Rma_Model_Rma) {
            return $this->_getTrackingUrl('rma_id', $track);
        } elseif ($track instanceof Magento_Rma_Model_Shipping) {
            return $this->_getTrackingUrl('track_id', $track, 'getEntityId');
        }
    }

    /**
     * Retrieve tracking url with params
     *
     * @param  string $key
     * @param  Magento_Rma_Model_Shipping|Magento_Rma_Model_Rma $model
     * @param  string $method - option
     * @return string
     */
    protected function _getTrackingUrl($key, $model, $method = 'getId')
    {
         $param = array(
             'hash' => $this->_coreData->urlEncode("{$key}:{$model->$method()}:{$model->getProtectCode()}")
         );

         $storeId = is_object($model) ? $model->getStoreId() : null;
         $storeModel = Mage::app()->getStore($storeId);
         return $storeModel->getUrl('rma/tracking/popup', $param);
    }

    /**
     * Decode url hash
     *
     * @param  string $hash
     * @return array
     */
    public function decodeTrackingHash($hash)
    {
        $hash = explode(':', $this->_coreData->urlDecode($hash));
        if (count($hash) === 3 && in_array($hash[0], $this->_allowedHashKeys)) {
            return array('key' => $hash[0], 'id' => (int)$hash[1], 'hash' => $hash[2]);
        }
        return array();
    }

    /**
     * Get whether selected product is returnable
     *
     * @param Magento_Catalog_Model_Product $product
     * @param int|null $storeId
     * @return bool
     */
    public function canReturnProduct($product, $storeId = null)
    {
        $isReturnable = $product->getIsReturnable();

        if (is_null($isReturnable)) {
            $isReturnable = Magento_Rma_Model_Product_Source::ATTRIBUTE_ENABLE_RMA_USE_CONFIG;
        }
        switch ($isReturnable) {
            case Magento_Rma_Model_Product_Source::ATTRIBUTE_ENABLE_RMA_YES:
                return true;
            case Magento_Rma_Model_Product_Source::ATTRIBUTE_ENABLE_RMA_NO:
                return false;
            default: //Use config and NULL
                return $this->_storeConfig->getConfig(Magento_Rma_Model_Product_Source::XML_PATH_PRODUCTS_ALLOWED, $storeId);
        }
    }

    /**
     * Get formated date in store timezone
     *
     * @param   string $date
     * @return  string
     */
    public function getFormatedDate($date)
    {
        $storeDate = Mage::app()->getLocale()
            ->storeDate(Mage::app()->getStore(), Magento_Date::toTimestamp($date), true);

        return $this->_coreData
            ->formatDate($storeDate, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
    }

    /**
     * Retrieves RMA item name for backend
     *
     * @param Magento_Sales_Model_Order_Item $item
     * @return string
     */
    public function getAdminProductName($item)
    {
        $name   = $item->getName();
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }

            if (!empty($result)) {
                $implode = array();
                foreach ($result as $val) {
                    $implode[] =  isset($val['print_value']) ? $val['print_value'] : $val['value'];
                }
                return $name.' ('.implode(', ', $implode).')';
            }
        }
        return $name;
    }

    /**
     * Retrieves RMA item sku for backend
     *
     * @param  Magento_Sales_Model_Order_Item $item
     * @return string
     */
    public function getAdminProductSku($item)
    {
        $name = $item->getSku();
        if ($item->getProductType() == Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $productOptions = $item->getProductOptions();

            return $productOptions['simple_sku'];
        }
        return $name;
    }

    /**
     * Parses quantity depending on isQtyDecimal flag
     *
     * @param float $quantity
     * @param Magento_Rma_Model_Item $item
     * @return int|float
     */
    public function parseQuantity($quantity, $item)
    {
        if (is_null($quantity)) {
             $quantity = $item->getOrigData('qty_requested');
        }
        if ($item->getIsQtyDecimal()) {
            return sprintf("%01.4f", $quantity);
        } else {
            return intval($quantity);
        }
    }

    /**
     * Get Qty by status
     *
     * @param Magento_Rma_Model_Item $item
     * @return int|float
     */
    public function getQty($item)
    {
        $qty = $item->getQtyRequested();

        if ($item->getQtyApproved()
            && ($item->getStatus() == Magento_Rma_Model_Rma_Source_Status::STATE_APPROVED)
        ) {
            $qty = $item->getQtyApproved();
        } elseif ($item->getQtyReturned()
            && ($item->getStatus() == Magento_Rma_Model_Rma_Source_Status::STATE_RECEIVED
                || $item->getStatus() == Magento_Rma_Model_Rma_Source_Status::STATE_REJECTED
            )
        ) {
            $qty = $item->getQtyReturned();
        } elseif ($item->getQtyAuthorized()
            && ($item->getStatus() == Magento_Rma_Model_Rma_Source_Status::STATE_AUTHORIZED)
        ) {
            $qty = $item->getQtyAuthorized();
        }

        return $this->parseQuantity($qty, $item);
    }
}
