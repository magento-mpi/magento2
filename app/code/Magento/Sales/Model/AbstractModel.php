<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model;

/**
 * Sales abstract model
 * Provide date processing functionality
 */
abstract class AbstractModel extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_localeDate = $localeDate;
        $this->dateTime = $dateTime;
    }

    /**
     * Get object store identifier
     *
     * @return int | string | \Magento\Store\Model\Store
     */
    abstract public function getStore();

    /**
     * Processing object after save data
     * Updates relevant grid table records.
     *
     * @return $this
     */
    public function afterCommitCallback()
    {
        if (!$this->getForceUpdateGridRecords()) {
            $this->_getResource()->updateGridRecords($this->getId());
        }
        return parent::afterCommitCallback();
    }

    /**
     * Get object created at date affected current active store timezone
     *
     * @return \Magento\Framework\Stdlib\DateTime\Date
     */
    public function getCreatedAtDate()
    {
        return $this->_localeDate->date($this->dateTime->toTimestamp($this->getCreatedAt()), null, null, true);
    }

    /**
     * Get object created at date affected with object store timezone
     *
     * @return \Magento\Framework\Stdlib\DateTime\Date
     */
    public function getCreatedAtStoreDate()
    {
        return $this->_localeDate->scopeDate(
            $this->getStore(),
            $this->dateTime->toTimestamp($this->getCreatedAt()),
            true
        );
    }
}
