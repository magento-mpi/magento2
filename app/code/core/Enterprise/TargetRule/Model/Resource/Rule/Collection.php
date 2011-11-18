<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Rule Resource Collection
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_TargetRule_Model_Resource_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource collection
     */
    protected function _construct()
    {
        $this->_init('Enterprise_TargetRule_Model_Rule', 'Enterprise_TargetRule_Model_Resource_Rule');
    }

    /**
     * Add Apply To Product List Filter to Collection
     *
     * @param int|array $applyTo
     * @return Enterprise_TargetRule_Model_Resource_Rule_Collection
     */
    public function addApplyToFilter($applyTo)
    {
        $this->addFieldToFilter('apply_to', $applyTo);
        return $this;
    }

    /**
     * Add Is active rule filter to collection
     *
     * @param int $isActive
     * @return Enterprise_TargetRule_Model_Resource_Rule_Collection
     */
    public function addIsActiveFilter($isActive = 1)
    {
        $this->addFieldToFilter('is_active', $isActive);
        return $this;
    }

    /**
     * Set Priority Sort order
     *
     * @param string $direction
     * @return Enterprise_TargetRule_Model_Resource_Rule_Collection
     */
    public function setPriorityOrder($direction = self::SORT_ORDER_ASC)
    {
        $this->setOrder('sort_order', $direction);
        return $this;
    }

    /**
     * After load collection load customer segment relation
     *
     * @return Enterprise_TargetRule_Model_Resource_Rule_Collection
     */
    protected function _afterLoad()
    {
        if ($this->getFlag('add_customersegment_relations')) {
            $this->getResource()->addCustomerSegmentRelationsToCollection($this);
        }

        foreach ($this->_items as $rule) {
            /* @var $rule Enterprise_TargetRule_Model_Rule */
            if (!$this->getFlag('do_not_run_after_load')) {
                $rule->afterLoad();
            }
        }

        return parent::_afterLoad();
    }

    /**
     * Add filter by product id to collection
     *
     * @param int $productId
     * @return Enterprise_TargetRule_Model_Resource_Rule_Collection
     */
    public function addProductFilter($productId)
    {
        $this->getSelect()->join(
            array('product_idx' => $this->getTable('enterprise_targetrule_product')),
            'product_idx.rule_id = main_table.rule_id',
            array()
        );
        $this->getSelect()->where('product_idx.product_id=?', $productId);

        return $this;
    }
}
