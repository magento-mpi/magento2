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
 * @method \Magento\ProductAlert\Model\Resource\Price _getResource()
 * @method \Magento\ProductAlert\Model\Resource\Price getResource()
 * @method int getCustomerId()
 * @method \Magento\ProductAlert\Model\Price setCustomerId(int $value)
 * @method int getProductId()
 * @method \Magento\ProductAlert\Model\Price setProductId(int $value)
 * @method float getPrice()
 * @method \Magento\ProductAlert\Model\Price setPrice(float $value)
 * @method int getWebsiteId()
 * @method \Magento\ProductAlert\Model\Price setWebsiteId(int $value)
 * @method string getAddDate()
 * @method \Magento\ProductAlert\Model\Price setAddDate(string $value)
 * @method string getLastSendDate()
 * @method \Magento\ProductAlert\Model\Price setLastSendDate(string $value)
 * @method int getSendCount()
 * @method \Magento\ProductAlert\Model\Price setSendCount(int $value)
 * @method int getStatus()
 * @method \Magento\ProductAlert\Model\Price setStatus(int $value)
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ProductAlert\Model;

class Price extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var \Magento\ProductAlert\Model\Resource\Price\Customer\CollectionFactory
     */
    protected $_customerColFactory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\ProductAlert\Model\Resource\Price\Customer\CollectionFactory $customerColFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\ProductAlert\Model\Resource\Price\Customer\CollectionFactory $customerColFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_customerColFactory = $customerColFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento\ProductAlert\Model\Resource\Price');
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
