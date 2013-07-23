<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report Products Tags collection
 *
 * @category    Mage
 * @package     Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_Resource_Reports_Collection extends Mage_Tag_Model_Resource_Popular_Collection
{
    /**
     * Add tag popularity to select by specified store ids
     *
     * @param int|array $storeIds
     * @return Mage_Tag_Model_Resource_Reports_Collection
     */
    public function addPopularity($storeIds)
    {
        $select = $this->getSelect()
            ->joinLeft(
                array('tr' => $this->getTable('tag_relation')),
                'main_table.tag_id = tr.tag_id AND tr.active = 1',
                array('popularity' => 'COUNT(tr.tag_id)')
            );
        if (!empty($storeIds)) {
            $select->where('tr.store_id IN(?)', $storeIds);
        }

        $select->group('main_table.tag_id');
        return $this;
    }
}
