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
 * @method Magento_Catalog_Model_Resource_Product_Compare_Item _getResource()
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
            $this->setStoreId(Mage::app()->getStore()->getId());
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

        Mage::helper('Magento_Catalog_Helper_Product_Compare')->setCustomerId($this->getCustomerId())->calculate();
        return $this;
    }

    /**
     * Customer logout bind process
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Catalog_Model_Product_Compare_Item
     */
    public function bindCustomerLogout(\Magento\Event\Observer $observer = null)
    {
        $this->_getResource()->purgeVisitorByCustomer($this);

        Mage::helper('Magento_Catalog_Helper_Product_Compare')->calculate(true);
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
            $customerId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId();
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
            $visitorId = Mage::getSingleton('Magento_Log_Model_Visitor')->getId();
            $this->setData('visitor_id', $visitorId);
        }
        return $this->getData('visitor_id');
    }
}
