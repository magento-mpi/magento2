<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry entity registrants collection
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftRegistry_Model_Resource_Person_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_GiftRegistry_Model_Person', 'Enterprise_GiftRegistry_Model_Resource_Person');
    }

    /**
     * Apply entity filter to collection
     *
     * @param int $entityId
     * @return Enterprise_GiftRegistry_Model_Resource_Person_Collection
     */
    public function addRegistryFilter($entityId)
    {
        $this->getSelect()->where('main_table.entity_id = ?', (int)$entityId);
        return $this;
    }
}
