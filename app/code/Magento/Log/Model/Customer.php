<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model;

/**
 * Customer log model
 *
 * @method \Magento\Log\Model\Resource\Customer _getResource()
 * @method \Magento\Log\Model\Resource\Customer getResource()
 * @method int getVisitorId()
 * @method \Magento\Log\Model\Customer setVisitorId(int $value)
 * @method int getCustomerId()
 * @method \Magento\Log\Model\Customer setCustomerId(int $value)
 * @method string getLoginAt()
 * @method \Magento\Log\Model\Customer setLoginAt(string $value)
 * @method string getLogoutAt()
 * @method \Magento\Log\Model\Customer setLogoutAt(string $value)
 * @method int getStoreId()
 * @method \Magento\Log\Model\Customer setStoreId(int $value)
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Customer extends \Magento\Model\AbstractModel
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Log\Model\Resource\Customer');
    }

    /**
     * Load last log by customer id
     *
     * @param int $customerId
     * @return \Magento\Log\Model\Customer
     */
    public function loadByCustomer($customerId)
    {
        return $this->load($customerId, 'customer_id');
    }

    /**
     * Return last login at in Unix time format
     *
     * @return int
     */
    public function getLoginAtTimestamp()
    {
        $loginAt = $this->getLoginAt();
        if ($loginAt) {
            return $this->dateTime->toTimestamp($loginAt);
        }

        return null;
    }
}
