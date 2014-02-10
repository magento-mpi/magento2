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
 * Sales abstract model
 * Provide date processing functionality
 */
namespace Magento\Sales\Model;

abstract class AbstractModel extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_coreLocale;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\LocaleInterface $coreLocale
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\LocaleInterface $coreLocale,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context, $registry, $resource, $resourceCollection, $data
        );
        $this->_coreLocale = $coreLocale;
        $this->dateTime = $dateTime;
    }

    /**
     * Get object store identifier
     *
     * @return int | string | \Magento\Core\Model\Store
     */
    abstract public function getStore();

    /**
     * Processing object after save data
     * Updates relevant grid table records.
     *
     * @return \Magento\Sales\Model\AbstractModel
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
     * @return \Zend_Date
     */
    public function getCreatedAtDate()
    {
        return $this->_coreLocale->date(
            $this->dateTime->toTimestamp($this->getCreatedAt()),
            null,
            null,
            true
        );
    }

    /**
     * Get object created at date affected with object store timezone
     *
     * @return \Zend_Date
     */
    public function getCreatedAtStoreDate()
    {
        return $this->_coreLocale->storeDate(
            $this->getStore(),
            $this->dateTime->toTimestamp($this->getCreatedAt()),
            true
        );
    }
}
