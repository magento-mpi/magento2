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
 * Staging action collection
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Staging_Action_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection initialization
     *
     */
    public function _construct()
    {
        $this->_init('Enterprise_Staging_Model_Staging_Action', 'Enterprise_Staging_Model_Resource_Staging_Action');
    }

    /**
     * Set staging filter into collection
     *
     * @param mixed $stagingId (if object must be implemented getId() method)
     * @return Enterprise_Staging_Model_Resource_Staging_Action_Collection
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
     * Set event filter into collection
     *
     * @param mixed $eventId (if object must be implemented getId() method)
     * @return Enterprise_Staging_Model_Resource_Staging_Action_Collection
     */
    public function setEventFilter($eventId)
    {
        if (is_object($eventId)) {
            $eventId = $eventId->getId();
        }
        $this->addFieldToFilter('event_id', (int) $eventId);

        return $this;
    }

    /**
     * Add staging to collection
     *
     * @return Enterprise_Staging_Model_Resource_Staging_Action_Collection
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
     * Add websites to collection
     *
     * @return Enterprise_Staging_Model_Resource_Staging_Action_Collection
     */
    public function addWebsitesToCollection()
    {
        $this->getSelect()
            ->joinLeft(
                array('core_website' => $this->getTable('core_website')),
                'main_table.master_website_id=core_website.website_id',
                array('master_website_id' => 'website_id',
                    'master_website_name' => 'name'))
            ->joinLeft(
                array('staging_website' => $this->getTable('core_website')),
                'main_table.staging_website_id=staging_website.website_id',
                array('staging_website_id' => 'website_id',
                    'staging_website_name' => 'name')
        );

        Mage::getResourceHelper('Mage_Core')->prepareColumnsList($this->getSelect());

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
        return parent::_toOptionArray('backup_id', 'name');
    }

    /**
     * Convert items array to hash for select options
     * array($value => $label)
     *
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('backup_id', 'name');
    }
}
