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
 * Staging collection
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Staging_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Staging_Model_Staging', 'Enterprise_Staging_Model_Resource_Staging');
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        $countSelect->columns('COUNT(main_table.staging_id)');

        return $countSelect;
    }

    /**
     * Set staging website filter into collection
     *
     * @param mixed $stagingWebsiteId (if object must be implemented getId() method)
     * @return Enterprise_Staging_Model_Resource_Staging_Collection
     */
    public function addStagingWebsiteToFilter($stagingWebsiteId)
    {
        if (is_object($stagingWebsiteId)) {
            $stagingWebsiteId = $stagingWebsiteId->getId();
        }
        $this->addFieldToFilter('staging_website_id', (int) $stagingWebsiteId);

        return $this;
    }

    /**
     * Joining website name
     *
     * @return Enterprise_Staging_Model_Resource_Staging_Collection
     */
    public function addWebsiteName()
    {
        $this->getSelect()->joinLeft(
            array('site'=>$this->getTable('core_website')),
            "main_table.staging_website_id = site.website_id",
            array('name' => 'site.name')
        );

       return $this;
    }

    /**
     * Joining last log id and log action
     *
     * @return Enterprise_Staging_Model_Resource_Staging_Collection
     */
    public function addLastLogComment()
    {
        $helper     = Mage::getResourceHelper('Enterprise_Staging');

        $subSelect = clone $this->getSelect();
        $subSelect->reset();
        $subSelect = $helper->getLastStagingLogQuery($this->getTable('enterprise_staging_log'), $subSelect);

        $this->getSelect()
            ->joinLeft(
                array('staging_log' => new Zend_Db_Expr('(' . $subSelect . ')')),
                'main_table.staging_id = staging_log.staging_id',
                array('log_id', 'action'));
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
        return parent::_toOptionArray('staging_id', 'name');
    }

    /**
     * Convert items array to hash for select options
     * array($value => $label)
     *
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('staging_id', 'name');
    }

    /**
     * Set staging is scheduled flag filter into collection
     *
     * @return Enterprise_Staging_Model_Resource_Staging_Collection
     */
    public function addIsSheduledToFilter()
    {
        $this->addFieldToFilter('merge_scheduling_date', array('notnull' => true));
        return $this;
    }
}
