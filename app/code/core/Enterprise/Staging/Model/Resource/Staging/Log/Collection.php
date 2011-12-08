<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Staging log collection
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Staging_Log_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection initialization
     *
     */
    public function _construct()
    {
        $this->_init('Enterprise_Staging_Model_Staging_Log', 'Enterprise_Staging_Model_Resource_Staging_Log');
    }

    /**
     * Set staging filter into collection
     *
     * @param mixed $stagingId (if object must be implemented getId() method)
     * @return Enterprise_Staging_Model_Resource_Staging_Log_Collection
     */
    public function setStagingFilter($stagingId)
    {
        if ($stagingId instanceof Varien_Object) {
            $stagingId = $stagingId->getId();
        }
        $this->addFieldToFilter('staging_id', (int) $stagingId);

        return $this;
    }

    /**
     * Joining staging table to collection
     *
     * @return Enterprise_Staging_Model_Resource_Staging_Log_Collection
     */
    public function addStagingToCollection()
    {
        $this->getSelect()
            ->joinLeft(
                array('staging' => $this->getTable('enterprise_staging')),
                'main_table.staging_id=staging.staging_id',
                array('staging_name'=>'name')
        );

        return $this;
    }

    /**
     * Convert items array to array for select options
     * array(
     *      $index => array(
     *          'value' => mixed
     *          'label' => mixed
     *      )
     * )
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('event_id', 'name');
    }

    /**
     * Convert items array to hash for select options
     * array($value => $label)
     *
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('event_id', 'name');
    }
}
