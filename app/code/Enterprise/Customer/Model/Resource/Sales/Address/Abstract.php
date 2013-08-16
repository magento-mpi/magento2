<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Sales Address abstract resource
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Customer_Model_Resource_Sales_Address_Abstract
    extends Enterprise_Customer_Model_Resource_Sales_Abstract
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
     * @return Enterprise_Customer_Model_Resource_Sales_Address_Abstract
     */
    public function attachDataToEntities(array $entities)
    {
        $items      = array();
        $itemIds    = array();
        foreach ($entities as $item) {
            /** @var $item Magento_Object */
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
