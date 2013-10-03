<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Sales Address abstract resource
 */
namespace Magento\CustomerCustomAttributes\Model\Resource\Sales\Address;

abstract class AbstractAddress
    extends \Magento\CustomerCustomAttributes\Model\Resource\Sales\AbstractSales
{
    /**
     * Used us prefix to name of column table
     *
     * @var null | string
     */
    protected $_columnPrefix     = null;

    /**
     * Attach data to models
     *
     * @param array $entities
     * @return \Magento\CustomerCustomAttributes\Model\Resource\Sales\Address\AbstractAddress
     */
    public function attachDataToEntities(array $entities)
    {
        $items      = array();
        $itemIds    = array();
        foreach ($entities as $item) {
            /** @var $item \Magento\Object */
            $itemIds[] = $item->getId();
            $items[$item->getId()] = $item;
        }

        if ($itemIds) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->where("{$this->getIdFieldName()} IN (?)", $itemIds);
            $rowSet = $this->_getReadAdapter()->fetchAll($select);
            foreach ($rowSet as $row) {
                $items[$row[$this->getIdFieldName()]]->addData($row);
            }
        }

        return $this;
    }
}
