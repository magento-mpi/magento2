<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Review summery collection
 *
 * @category    Magento
 * @package     Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Review\Model\Resource\Review\Summary;

class Collection extends \Magento\Data\Collection\Db
{
    /**
     * @var string
     */
    protected $_summaryTable;

    /**
     * @param Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\Resource $resource
     */
    public function __construct(
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\Resource $resource
    ) {
        $this->_setIdFieldName('primary_id');

        parent::__construct($fetchStrategy, $resource->getConnection('review_read'));
        $this->_summaryTable = $resource->getTableName('review_entity_summary');

        $this->_select->from($this->_summaryTable);

        $this->setItemObjectClass('Magento\Review\Model\Review\Summary');
    }

    /**
     * Add entity filter
     *
     * @param int|string $entityId
     * @param int $entityType
     * @return \Magento\Review\Model\Resource\Review\Summary\Collection
     */
    public function addEntityFilter($entityId, $entityType = 1)
    {
        $this->_select->where('entity_pk_value IN(?)', $entityId)
            ->where('entity_type = ?', $entityType);
        return $this;
    }

    /**
     * Add store filter
     *
     * @param int $storeId
     * @return \Magento\Review\Model\Resource\Review\Summary\Collection
     */
    public function addStoreFilter($storeId)
    {
        $this->_select->where('store_id = ?', $storeId);
        return $this;
    }
}
