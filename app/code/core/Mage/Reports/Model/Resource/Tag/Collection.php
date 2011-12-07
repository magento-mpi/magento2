<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Report Products Tags collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Tag_Collection extends Mage_Tag_Model_Resource_Popular_Collection
{
    /**
     * Add tag popularity to select by specified store ids
     *
     * @param int|array $storeIds
     * @return Mage_Reports_Model_Resource_Tag_Collection
     */
    public function addPopularity($storeIds)
    {
        $select = $this->getSelect()
            ->joinLeft(
                array('tr' => $this->getTable('tag_relation')),
                'main_table.tag_id = tr.tag_id',
                array('popularity' => 'COUNT(tr.tag_id)')
            );
        if (!empty($storeIds)) {
            $select->where('tr.store_id IN(?)', $storeIds);
        }
        $select->group('main_table.tag_id');

        /**
         * Allow to use analytic function
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }
}
