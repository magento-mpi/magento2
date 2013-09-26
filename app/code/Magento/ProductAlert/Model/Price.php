<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ProductAlert for changed price model
 *
 * @method Magento_ProductAlert_Model_Resource_Price _getResource()
 * @method Magento_ProductAlert_Model_Resource_Price getResource()
 * @method int getCustomerId()
 * @method Magento_ProductAlert_Model_Price setCustomerId(int $value)
 * @method int getProductId()
 * @method Magento_ProductAlert_Model_Price setProductId(int $value)
 * @method float getPrice()
 * @method Magento_ProductAlert_Model_Price setPrice(float $value)
 * @method int getWebsiteId()
 * @method Magento_ProductAlert_Model_Price setWebsiteId(int $value)
 * @method string getAddDate()
 * @method Magento_ProductAlert_Model_Price setAddDate(string $value)
 * @method string getLastSendDate()
 * @method Magento_ProductAlert_Model_Price setLastSendDate(string $value)
 * @method int getSendCount()
 * @method Magento_ProductAlert_Model_Price setSendCount(int $value)
 * @method int getStatus()
 * @method Magento_ProductAlert_Model_Price setStatus(int $value)
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ProductAlert_Model_Price extends Magento_Core_Model_Abstract
{
    /**
     * @var Magento_ProductAlert_Model_Resource_Price_Customer_CollectionFactory
     */
    protected $_customerColFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_ProductAlert_Model_Resource_Price_Customer_CollectionFactory $customerColFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_ProductAlert_Model_Resource_Price_Customer_CollectionFactory $customerColFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_customerColFactory = $customerColFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento_ProductAlert_Model_Resource_Price');
    }

    public function getCustomerCollection()
    {
        return $this->_customerColFactory->create();
    }

    public function loadByParam()
    {
        if (!is_null($this->getProductId()) && !is_null($this->getCustomerId()) && !is_null($this->getWebsiteId())) {
            $this->getResource()->loadByParam($this);
        }
        return $this;
    }

    public function deleteCustomer($customerId, $websiteId = 0)
    {
        $this->getResource()->deleteCustomer($this, $customerId, $websiteId);
        return $this;
    }
}
