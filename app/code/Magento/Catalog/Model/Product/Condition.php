<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

class Condition extends \Magento\Object implements \Magento\Catalog\Model\Product\Condition\ConditionInterface
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
