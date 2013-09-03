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
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Sales_Model_Abstract extends Magento_Core_Model_Abstract
{
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
        return Mage::app()->getLocale()->date(
            \Magento\Date::toTimestamp($this->getCreatedAt()),
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
        return Mage::app()->getLocale()->storeDate(
            $this->getStore(),
            \Magento\Date::toTimestamp($this->getCreatedAt()),
            true
        );
    }
}
