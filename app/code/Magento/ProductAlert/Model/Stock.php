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
 * ProductAlert for back in stock model
 *
 * @method Magento_ProductAlert_Model_Resource_Stock _getResource()
 * @method Magento_ProductAlert_Model_Resource_Stock getResource()
 * @method int getCustomerId()
 * @method Magento_ProductAlert_Model_Stock setCustomerId(int $value)
 * @method int getProductId()
 * @method Magento_ProductAlert_Model_Stock setProductId(int $value)
 * @method int getWebsiteId()
 * @method Magento_ProductAlert_Model_Stock setWebsiteId(int $value)
 * @method string getAddDate()
 * @method Magento_ProductAlert_Model_Stock setAddDate(string $value)
 * @method string getSendDate()
 * @method Magento_ProductAlert_Model_Stock setSendDate(string $value)
 * @method int getSendCount()
 * @method Magento_ProductAlert_Model_Stock setSendCount(int $value)
 * @method int getStatus()
 * @method Magento_ProductAlert_Model_Stock setStatus(int $value)
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ProductAlert_Model_Stock extends Magento_Core_Model_Abstract
{
    /**
     * @var Magento_ProductAlert_Model_Resource_Stock_Customer_CollectionFactory
     */
    protected $_customerColFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_ProductAlert_Model_Resource_Stock_Customer_CollectionFactory $customerColFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_ProductAlert_Model_Resource_Stock_Customer_CollectionFactory $customerColFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_customerColFactory = $customerColFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento_ProductAlert_Model_Resource_Stock');
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
