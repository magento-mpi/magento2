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
class Customer extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
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
     * @param \Magento\Customer\Model\Customer|int $customer
     * @return $this
     */
    public function loadByCustomer($customer)
    {
        if ($customer instanceof \Magento\Customer\Model\Customer) {
            $customer = $customer->getId();
        }

        return $this->load($customer, 'customer_id');
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
