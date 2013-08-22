<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry entity registrants collection
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftRegistry_Model_Resource_Person_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_GiftRegistry_Model_Person', 'Magento_GiftRegistry_Model_Resource_Person');
    }

    /**
     * Apply entity filter to collection
     *
     * @param int $entityId
     * @return Magento_GiftRegistry_Model_Resource_Person_Collection
     */
    public function addRegistryFilter($entityId)
    {
        $this->getSelect()->where('main_table.entity_id = ?', (int)$entityId);
        return $this;
    }
}
