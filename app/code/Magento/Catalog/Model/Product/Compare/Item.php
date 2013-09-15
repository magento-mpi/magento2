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
 * @method \Magento\Catalog\Model\Resource\Product\Compare\Item getResource()
 * @method \Magento\Catalog\Model\Product\Compare\Item setVisitorId(int $value)
 * @method \Magento\Catalog\Model\Product\Compare\Item setCustomerId(int $value)
 * @method int getProductId()
 * @method \Magento\Catalog\Model\Product\Compare\Item setProductId(int $value)
 * @method int getStoreId()
 * @method \Magento\Catalog\Model\Product\Compare\Item setStoreId(int $value)
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Compare;

class Item extends \Magento\Core\Model\AbstractModel
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
     * @param Magento_Catalog_Helper_Product_Compare $catalogProductCompare
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Compare $catalogProductCompare,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogProductCompare = $catalogProductCompare;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resourse model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\Resource\Product\Compare\Item');
    }

    /**
     * Retrieve Resource instance
     *
     * @return \Magento\Catalog\Model\Resource\Product\Compare\Item
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Set current store before save
     *
     * @return \Magento\Catalog\Model\Product\Compare\Item
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->hasStoreId()) {
            $this->setStoreId(\Mage::app()->getStore()->getId());
        }

        return $this;
    }

    /**
     * Add customer data from customer object
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return \Magento\Catalog\Model\Product\Compare\Item
     */
    public function addCustomerData(\Magento\Customer\Model\Customer $customer)
    {
        $this->setCustomerId($customer->getId());
        return $this;
    }

    /**
     * Set visitor
     *
     * @param int $visitorId
     * @return \Magento\Catalog\Model\Product\Compare\Item
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
     * @return \Magento\Catalog\Model\Product\Compare\Item
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
     * @return \Magento\Catalog\Model\Product\Compare\Item
     */
    public function addProductData($product)
    {
        if ($product instanceof \Magento\Catalog\Model\Product) {
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
     * @return \Magento\Catalog\Model\Product\Compare\Item
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Catalog\Model\Product\Compare\Item
     */
    public function bindCustomerLogout(\Magento\Event\Observer $observer = null)
    {
        $this->_getResource()->purgeVisitorByCustomer($this);

        $this->_catalogProductCompare->calculate(true);
        return $this;
    }

    /**
     * Clean compare items
     *
     * @return \Magento\Catalog\Model\Product\Compare\Item
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
            $customerId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId();
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
            $visitorId = \Mage::getSingleton('Magento\Log\Model\Visitor')->getId();
            $this->setData('visitor_id', $visitorId);
        }
        return $this->getData('visitor_id');
    }
}
