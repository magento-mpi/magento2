<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Model_Product_Condition extends Magento_Object implements Magento_Catalog_Model_Product_Condition_Interface
{
    public function applyToCollection($collection)
    {
        if ($this->getTable() && $this->getPkFieldName()) {
            $collection->joinTable(
                $this->getTable(),
                $this->getPkFieldName().'=entity_id',
                array('affected_product_id'=>$this->getPkFieldName())
            );
        }
        return $this;
    }

    public function getIdsSelect($dbAdapter)
    {
        if ($this->getTable() && $this->getPkFieldName()) {
            $select = $dbAdapter->select()
                ->from($this->getTable(), $this->getPkFieldName());
            return $select;
        }
        return '';
    }
}
