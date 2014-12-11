<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerCustomAttributes\Model\Resource\Sales\Address;

/**
 * Customer Sales Address abstract resource
 */
abstract class AbstractAddress extends \Magento\CustomerCustomAttributes\Model\Resource\Sales\AbstractSales
{
    /**
     * Used us prefix to name of column table
     *
     * @var null | string
     */
    protected $_columnPrefix = null;

    /**
     * Attach data to models
     *
     * @param \Magento\Framework\Object[] $entities
     * @return $this
     */
    public function attachDataToEntities(array $entities)
    {
        $items = [];
        $itemIds = [];
        foreach ($entities as $item) {
            /** @var $item \Magento\Framework\Object */
            $itemIds[] = $item->getId();
            $items[$item->getId()] = $item;
        }

        if ($itemIds) {
            $select = $this->_getReadAdapter()->select()->from(
                $this->getMainTable()
            )->where(
                "{$this->getIdFieldName()} IN (?)",
                $itemIds
            );
            $rowSet = $this->_getReadAdapter()->fetchAll($select);
            foreach ($rowSet as $row) {
                $items[$row[$this->getIdFieldName()]]->addData($row);
            }
        }

        return $this;
    }
}
