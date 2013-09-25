<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Compare Item Model
 *
 * @method Magento_Catalog_Model_Resource_Product_Compare_Item getResource()
 * @method Magento_Catalog_Model_Product_Compare_Item setVisitorId(int $value)
 * @method Magento_Catalog_Model_Product_Compare_Item setCustomerId(int $value)
 * @method int getProductId()
 * @method Magento_Catalog_Model_Product_Compare_Item setProductId(int $value)
 * @method int getStoreId()
 * @method Magento_Catalog_Model_Product_Compare_Item setStoreId(int $value)
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Compare_Item extends Magento_Core_Model_Abstract
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'catalog_compare_item';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getItem() in this case
     *
     * @var string
     */
    protected $_eventObject = 'item';

    /**
     * Catalog product compare
     *
     * @var Magento_Catalog_Helper_Product_Compare
     */
    protected $_catalogProductCompare = null;

    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Log visitor
     *
     * @var Magento_Log_Model_Visitor
     */
    protected $_logVisitor;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Log_Model_Visitor $logVisitor
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Catalog_Helper_Product_Compare $catalogProductCompare
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Log_Model_Visitor $logVisitor,
        Magento_Customer_Model_Session $customerSession,
        Magento_Catalog_Helper_Product_Compare $catalogProductCompare,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_logVisitor = $logVisitor;
        $this->_customerSession = $customerSession;
        $this->_catalogProductCompare = $catalogProductCompare;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resourse model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Resource_Product_Compare_Item');
    }

    /**
     * Retrieve Resource instance
     *
     * @return Magento_Catalog_Model_Resource_Product_Compare_Item
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Set current store before save
     *
     * @return Magento_Catalog_Model_Product_Compare_Item
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->hasStoreId()) {
            $this->setStoreId($this->_storeManager->getStore()->getId());
        }

        return $this;
    }

    /**
     * Add customer data from customer object
     *
     * @param Magento_Customer_Model_Customer $customer
     * @return Magento_Catalog_Model_Product_Compare_Item
     */
    public function addCustomerData(Magento_Customer_Model_Customer $customer)
    {
        $this->setCustomerId($customer->getId());
        return $this;
    }

    /**
     * Set visitor
     *
     * @param int $visitorId
     * @return Magento_Catalog_Model_Product_Compare_Item
     */
    public function addVisitorId($visitorId)
    {
        $this->setVisitorId($visitorId);
        return $this;
    }

    /**
     * Load compare item by product
     *
     * @param mixed $product
     * @return Magento_Catalog_Model_Product_Compare_Item
     */
    public function loadByProduct($product)
    {
        $this->_getResource()->loadByProduct($this, $product);
        return $this;
    }

    /**
     * Set product data
     *
     * @param mixed $product
     * @return Magento_Catalog_Model_Product_Compare_Item
     */
    public function addProductData($product)
    {
        if ($product instanceof Magento_Catalog_Model_Product) {
            $this->setProductId($product->getId());
        }
        else if(intval($product)) {
            $this->setProductId(intval($product));
        }

        return $this;
    }

    /**
     * Retrieve data for save
     *
     * @return array
     */
    public function getDataForSave()
    {
        $data = array();
        $data['customer_id'] = $this->getCustomerId();
        $data['visitor_id']  = $this->getVisitorId();
        $data['product_id']  = $this->getProductId();

        return $data;
    }

    /**
     * Customer login bind process
     *
     * @return Magento_Catalog_Model_Product_Compare_Item
     */
    public function bindCustomerLogin()
    {
        $this->_getResource()->updateCustomerFromVisitor($this);

        $this->_catalogProductCompare->setCustomerId($this->getCustomerId())->calculate();
        return $this;
    }

    /**
     * Customer logout bind process
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Product_Compare_Item
     */
    public function bindCustomerLogout(Magento_Event_Observer $observer = null)
    {
        $this->_getResource()->purgeVisitorByCustomer($this);

        $this->_catalogProductCompare->calculate(true);
        return $this;
    }

    /**
     * Clean compare items
     *
     * @return Magento_Catalog_Model_Product_Compare_Item
     */
    public function clean()
    {
        $this->_getResource()->clean($this);
        return $this;
    }

    /**
     * Retrieve Customer Id if loggined
     *
     * @return int
     */
    public function getCustomerId()
    {
        if (!$this->hasData('customer_id')) {
            $customerId = $this->_customerSession->getCustomerId();
            $this->setData('customer_id', $customerId);
        }
        return $this->getData('customer_id');
    }

    /**
     * Retrieve Visitor Id
     *
     * @return int
     */
    public function getVisitorId()
    {
        if (!$this->hasData('visitor_id')) {
            $visitorId = $this->_logVisitor->getId();
            $this->setData('visitor_id', $visitorId);
        }
        return $this->getData('visitor_id');
    }
}
