<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * History resource collection
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_History_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('Mage_XmlConnect_Model_History', 'Mage_XmlConnect_Model_Resource_History');
    }

    /**
     * Filter collection by store
     *
     * @param int $storeId
     * @return Mage_XmlConnect_Model_Resource_History_Collection
     */
    public function addStoreFilter($storeId)
    {
        $this->addFieldToFilter('store_id', $storeId);
        return $this;
    }

    /**
     * Filter collection by application_id
     *
     * @param int $applicationId
     * @return Mage_XmlConnect_Model_Resource_History_Collection
     */
    public function addApplicationFilter($applicationId)
    {
        $this->addFieldToFilter('application_id', $applicationId);
        return $this;
    }
}
