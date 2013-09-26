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
abstract class Magento_Sales_Model_Abstract extends Magento_Core_Model_Abstract
{
    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_coreLocale;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_LocaleInterface $coreLocale
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_LocaleInterface $coreLocale,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
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
     * @return int | string | Magento_Core_Model_Store
     */
    abstract public function getStore();

    /**
     * Processing object after save data
     * Updates relevant grid table records.
     *
     * @return Magento_Sales_Model_Abstract
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
     * @return Zend_Date
     */
    public function getCreatedAtDate()
    {
        return $this->_coreLocale->date(
            Magento_Date::toTimestamp($this->getCreatedAt()),
            null,
            null,
            true
        );
    }

    /**
     * Get object created at date affected with object store timezone
     *
     * @return Zend_Date
     */
    public function getCreatedAtStoreDate()
    {
        return $this->_coreLocale->storeDate(
            $this->getStore(),
            Magento_Date::toTimestamp($this->getCreatedAt()),
            true
        );
    }
}
