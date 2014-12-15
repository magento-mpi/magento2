<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Relations between a banner and customer segments
 */
namespace Magento\BannerCustomerSegment\Model\Resource;

class BannerSegmentLink extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Setup association with a table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_banner_customersegment', null);
    }

    /**
     * Load and return identifiers of customer segments associated with a banner
     *
     * @param int $bannerId
     * @return array
     */
    public function loadBannerSegments($bannerId)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->getMainTable(),
            'segment_id'
        )->where(
            'banner_id = ?',
            $bannerId
        );
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Update customer segments associated with a banner by overriding existing relations
     *
     * @param int $bannerId
     * @param array $segmentIds
     * @return void
     */
    public function saveBannerSegments($bannerId, array $segmentIds)
    {
        foreach ($segmentIds as $segmentId) {
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getMainTable(),
                ['banner_id' => $bannerId, 'segment_id' => $segmentId],
                ['banner_id']
            );
        }
        if (!$segmentIds) {
            $segmentIds = [0];
        }
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            ['banner_id = ?' => $bannerId, 'segment_id NOT IN (?)' => $segmentIds]
        );
    }

    /**
     * Limit the scope of a select object to certain customer segments
     *
     * @param \Zend_Db_Select $select
     * @param array $segmentIds
     * @return void
     */
    public function addBannerSegmentFilter(\Zend_Db_Select $select, array $segmentIds)
    {
        $select->joinLeft(
            ['banner_segment' => $this->getMainTable()],
            'banner_segment.banner_id = main_table.banner_id',
            []
        );
        if ($segmentIds) {
            $select->where('banner_segment.segment_id IS NULL OR banner_segment.segment_id IN (?)', $segmentIds);
        } else {
            $select->where('banner_segment.segment_id IS NULL');
        }
    }
}
