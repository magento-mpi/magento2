<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Resource\Calculation\Rule;

/**
 * Tax rule collection
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Tax\Model\Calculation\Rule', 'Magento\Tax\Model\Resource\Calculation\Rule');
    }

    /**
     * Join calculation data to result
     *
     * @param string $alias table alias
     * @return \Magento\Tax\Model\Resource\Calculation\Rule\Collection
     */
    public function joinCalculationData($alias)
    {
        $this->getSelect()->joinLeft(
            array($alias => $this->getTable('tax_calculation')),
            "main_table.tax_calculation_rule_id = {$alias}.tax_calculation_rule_id",
            array()
        );
        $this->getSelect()->group('main_table.tax_calculation_rule_id');

        return $this;
    }

    /**
     * Join tax data to collection
     *
     * @param string $itemTable
     * @param string $primaryJoinField
     * @param string $secondaryJoinField
     * @param string $titleField
     * @param string $dataField
     * @return \Magento\Tax\Model\Resource\Calculation\Rule\Collection
     */
    protected function _add($itemTable, $primaryJoinField, $secondaryJoinField, $titleField, $dataField)
    {
        $children = array();
        foreach ($this as $rule) {
            $children[$rule->getId()] = array();
        }
        if (!empty($children)) {
            $joinCondition = sprintf('item.%s = calculation.%s', $secondaryJoinField, $primaryJoinField);
            $select = $this->getConnection()->select()
                ->from(
                    array('calculation' => $this->getTable('tax_calculation')),
                    array('calculation.tax_calculation_rule_id')
                )
                ->join(
                    array('item' => $this->getTable($itemTable)),
                    $joinCondition,
                    array("item.{$titleField}", "item.{$secondaryJoinField}")
                )
                ->where('calculation.tax_calculation_rule_id IN (?)', array_keys($children))
                ->distinct(true);

            $data = $this->getConnection()->fetchAll($select);
            foreach ($data as $row) {
               $children[$row['tax_calculation_rule_id']][$row[$secondaryJoinField]] = $row[$titleField];
            }
        }

        foreach ($this as $rule) {
            if (isset($children[$rule->getId()])) {
                $rule->setData($dataField, array_keys($children[$rule->getId()]));
            }
        }

        return $this;
    }

    /**
     * Add product tax classes to result
     *
     * @return \Magento\Tax\Model\Resource\Calculation\Rule\Collection
     */
    public function addProductTaxClassesToResult()
    {
        return $this->_add('tax_class', 'product_tax_class_id', 'class_id', 'class_name', 'product_tax_classes');
    }

    /**
     * Add customer tax classes to result
     *
     * @return \Magento\Tax\Model\Resource\Calculation\Rule\Collection
     */
    public function addCustomerTaxClassesToResult()
    {
        return $this->_add('tax_class', 'customer_tax_class_id', 'class_id', 'class_name', 'customer_tax_classes');
    }

    /**
     * Add rates to result
     *
     * @return \Magento\Tax\Model\Resource\Calculation\Rule\Collection
     */
    public function addRatesToResult()
    {
        return $this->_add('tax_calculation_rate', 'tax_calculation_rate_id', 'tax_calculation_rate_id', 'code', 'tax_rates');
    }

    /**
     * Add class type filter
     *
     * @param string $type
     * @param int $id
     * @return \Magento\Tax\Model\Resource\Calculation\Rule\Collection
     * @throws \Magento\Core\Exception
     */
    public function setClassTypeFilter($type, $id)
    {
        switch ($type) {
            case \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT:
                $field = 'cd.product_tax_class_id';
                break;
            case \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER:
                $field = 'cd.customer_tax_class_id';
                break;
            default:
                throw new \Magento\Core\Exception('Invalid type supplied');
        }

        $this->joinCalculationData('cd');
        $this->addFieldToFilter($field, $id);
        return $this;
    }
}
