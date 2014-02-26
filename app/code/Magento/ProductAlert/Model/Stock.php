<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Model;

use Magento\Customer\Model\Resource\Customer\Collection;

/**
 * ProductAlert for back in stock model
 *
 * @method \Magento\ProductAlert\Model\Resource\Stock _getResource()
 * @method \Magento\ProductAlert\Model\Resource\Stock getResource()
 * @method int getCustomerId()
 * @method \Magento\ProductAlert\Model\Stock setCustomerId(int $value)
 * @method int getProductId()
 * @method \Magento\ProductAlert\Model\Stock setProductId(int $value)
 * @method int getWebsiteId()
 * @method \Magento\ProductAlert\Model\Stock setWebsiteId(int $value)
 * @method string getAddDate()
 * @method \Magento\ProductAlert\Model\Stock setAddDate(string $value)
 * @method string getSendDate()
 * @method \Magento\ProductAlert\Model\Stock setSendDate(string $value)
 * @method int getSendCount()
 * @method \Magento\ProductAlert\Model\Stock setSendCount(int $value)
 * @method int getStatus()
 * @method \Magento\ProductAlert\Model\Stock setStatus(int $value)
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Stock extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var \Magento\ProductAlert\Model\Resource\Stock\Customer\CollectionFactory
     */
    protected $_customerColFactory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\ProductAlert\Model\Resource\Stock\Customer\CollectionFactory $customerColFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\ProductAlert\Model\Resource\Stock\Customer\CollectionFactory $customerColFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_customerColFactory = $customerColFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\ProductAlert\Model\Resource\Stock');
    }

    /**
     * @return Collection
     */
    public function getCustomerCollection()
    {
        return $this->_customerColFactory->create();
    }

    /**
     * @return $this
     */
    public function loadByParam()
    {
        if (!is_null($this->getProductId()) && !is_null($this->getCustomerId()) && !is_null($this->getWebsiteId())) {
            $this->getResource()->loadByParam($this);
        }
        return $this;
    }

    /**
     * @param int $customerId
     * @param int $websiteId
     * @return $this
     */
    public function deleteCustomer($customerId, $websiteId = 0)
    {
        $this->getResource()->deleteCustomer($this, $customerId, $websiteId);
        return $this;
    }
}
