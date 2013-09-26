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
 * Sales order address model
 *
 * @method Magento_Sales_Model_Resource_Order_Address _getResource()
 * @method Magento_Sales_Model_Resource_Order_Address getResource()
 * @method int getParentId()
 * @method Magento_Sales_Model_Order_Address setParentId(int $value)
 * @method int getCustomerAddressId()
 * @method Magento_Sales_Model_Order_Address setCustomerAddressId(int $value)
 * @method int getQuoteAddressId()
 * @method Magento_Sales_Model_Order_Address setQuoteAddressId(int $value)
 * @method Magento_Sales_Model_Order_Address setRegionId(int $value)
 * @method int getCustomerId()
 * @method Magento_Sales_Model_Order_Address setCustomerId(int $value)
 * @method string getFax()
 * @method Magento_Sales_Model_Order_Address setFax(string $value)
 * @method Magento_Sales_Model_Order_Address setRegion(string $value)
 * @method string getPostcode()
 * @method Magento_Sales_Model_Order_Address setPostcode(string $value)
 * @method string getLastname()
 * @method Magento_Sales_Model_Order_Address setLastname(string $value)
 * @method string getCity()
 * @method Magento_Sales_Model_Order_Address setCity(string $value)
 * @method string getEmail()
 * @method Magento_Sales_Model_Order_Address setEmail(string $value)
 * @method string getTelephone()
 * @method Magento_Sales_Model_Order_Address setTelephone(string $value)
 * @method string getCountryId()
 * @method Magento_Sales_Model_Order_Address setCountryId(string $value)
 * @method string getFirstname()
 * @method Magento_Sales_Model_Order_Address setFirstname(string $value)
 * @method string getAddressType()
 * @method Magento_Sales_Model_Order_Address setAddressType(string $value)
 * @method string getPrefix()
 * @method Magento_Sales_Model_Order_Address setPrefix(string $value)
 * @method string getMiddlename()
 * @method Magento_Sales_Model_Order_Address setMiddlename(string $value)
 * @method string getSuffix()
 * @method Magento_Sales_Model_Order_Address setSuffix(string $value)
 * @method string getCompany()
 * @method Magento_Sales_Model_Order_Address setCompany(string $value)
 */
class Magento_Sales_Model_Order_Address extends Magento_Customer_Model_Address_Abstract
{
    /**
     * @var Magento_Sales_Model_Order
     */
    protected $_order;

    /**
     * @var string
     */
    protected $_eventPrefix = 'sales_order_address';

    /**
     * @var string
     */
    protected $_eventObject = 'address';

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Directory_Helper_Data $directoryData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Directory_Helper_Data $directoryData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $eventManager,
            $directoryData,
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_orderFactory = $orderFactory;
    }

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Address');
    }

    /**
     * Set order
     *
     * @param Magento_Sales_Model_Order $order
     * @return Magento_Sales_Model_Order_Address
     */
    public function setOrder(Magento_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = $this->_orderFactory->create()->load($this->getParentId());
        }
        return $this->_order;
    }

    /**
     * Before object save manipulations
     *
     * @return Magento_Sales_Model_Order_Address
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getOrder()) {
            $this->setParentId($this->getOrder()->getId());
        }

        // Init customer address id if customer address is assigned
        if ($this->getCustomerAddress()) {
            $this->setCustomerAddressId($this->getCustomerAddress()->getId());
        }

        return $this;
    }
}
