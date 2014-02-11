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

use Magento\Catalog\Model\Product;

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
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $_catalogProductCompare = null;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Log visitor
     *
     * @var \Magento\Log\Model\Visitor
     */
    protected $_logVisitor;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Log\Model\Visitor $logVisitor
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Helper\Product\Compare $catalogProductCompare
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Log\Model\Visitor $logVisitor,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Helper\Product\Compare $catalogProductCompare,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
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
     * @return void
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
     * @return $this
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
     * @param \Magento\Customer\Model\Customer $customer
     * @return $this
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
     * @return $this
     */
    public function addVisitorId($visitorId)
    {
        $this->setVisitorId($visitorId);
        return $this;
    }

    /**
     * Load compare item by product
     *
     * @param Product|int $product
     * @return $this
     */
    public function loadByProduct($product)
    {
        $this->_getResource()->loadByProduct($this, $product);
        return $this;
    }

    /**
     * Set product data
     *
     * @param Product|int $product
     * @return $this
     */
    public function addProductData($product)
    {
        if ($product instanceof Product) {
            $this->setProductId($product->getId());
        } elseif (intval($product)) {
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
