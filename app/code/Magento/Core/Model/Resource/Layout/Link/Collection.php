<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout update collection model
 */
class Magento_Core_Model_Resource_Layout_Link_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Core_Model_Layout_Link', 'Magento_Core_Model_Resource_Layout_Link');
    }

    /**
     * Add filter by theme id
     *
     * @param int $themeId
     * @return $this
     */
    public function addThemeFilter($themeId)
    {
        $this->addFieldToFilter('theme_id', $themeId);
        return $this;
    }

    /**
     * Join with layout update table
     *
     * @param array $fields
     * @return Magento_Core_Model_Resource_Layout_Link_Collection
     */
    protected function _joinWithUpdate($fields = array())
    {
        $flagName = 'joined_with_update_table';
        if (!$this->getFlag($flagName)) {
            $this->getSelect()
                ->join(
                    array('update' => $this->getTable('core_layout_update')),
                    'update.layout_update_id = main_table.layout_update_id',
                    array($fields)
                );
            $this->setFlag($flagName, true);
        }

        return $this;
    }

    /**
     * Filter by temporary flag
     *
     * @param bool $isTemporary
     * @return Magento_Core_Model_Resource_Layout_Link_Collection
     */
    public function addTemporaryFilter($isTemporary)
    {
        $this->addFieldToFilter('main_table.is_temporary', $isTemporary ? 1 : 0);
        return $this;
    }

    /**
     * Get links for layouts that are older then specified number of days
     *
     * @param $days
     * @return Magento_Core_Model_Resource_Layout_Link_Collection
     */
    public function addUpdatedDaysBeforeFilter($days)
    {
        $datetime = new DateTime();
        $storeInterval = new DateInterval('P' . $days . 'D');
        $datetime->sub($storeInterval);
        $formattedDate = $this->formatDate($datetime->getTimestamp());

        $this->_joinWithUpdate();
        $this->addFieldToFilter('update.updated_at', array('notnull' => true))
            ->addFieldToFilter('update.updated_at', array('lt' => $formattedDate));

        return $this;
    }
}
