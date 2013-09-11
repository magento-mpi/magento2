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
namespace Magento\Sales\Model;

abstract class AbstractModel extends \Magento\Core\Model\AbstractModel
{
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
        return \Mage::app()->getLocale()->date(
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
        return \Mage::app()->getLocale()->storeDate(
            $this->getStore(),
            \Magento\Date::toTimestamp($this->getCreatedAt()),
            true
        );
    }
}
