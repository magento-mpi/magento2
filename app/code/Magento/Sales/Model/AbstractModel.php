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
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\LocaleInterface $coreLocale
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\LocaleInterface $coreLocale,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    )
    {
        parent::__construct(
            $context, $registry, $resource, $resourceCollection, $data
        );
        $this->_coreLocale = $coreLocale;
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
            \Magento\Date::toTimestamp($this->getCreatedAt()),
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
            \Magento\Date::toTimestamp($this->getCreatedAt()),
            true
        );
    }
}
