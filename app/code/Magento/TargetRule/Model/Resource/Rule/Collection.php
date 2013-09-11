<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Target rules resource collection model
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\TargetRule\Model\Resource\Rule;

class Collection extends \Magento\Rule\Model\Resource\Rule\Collection\AbstractCollection
{
    /**
     * Set resource model
     */
    protected function _construct()
    {
        $this->_init('\Magento\TargetRule\Model\Rule', '\Magento\TargetRule\Model\Resource\Rule');
    }

    /**
     * Run "afterLoad" callback on items if it is applicable
     *
     * @return \Magento\TargetRule\Model\Resource\Rule\Collection
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $rule) {
            /* @var $rule \Magento\TargetRule\Model\Rule */
            if (!$this->getFlag('do_not_run_after_load')) {
                $rule->afterLoad();
            }
        }

        parent::_afterLoad();
        return $this;
    }

    /**
     * Add Apply To Product List Filter to Collection
     *
     * @param int|array $applyTo
     *
     * @return \Magento\TargetRule\Model\Resource\Rule\Collection
     */
    public function addApplyToFilter($applyTo)
    {
        $this->addFieldToFilter('apply_to', $applyTo);
        return $this;
    }

    /**
     * Set Priority Sort order
     *
     * @param string $direction
     *
     * @return \Magento\TargetRule\Model\Resource\Rule\Collection
     */
    public function setPriorityOrder($direction = self::SORT_ORDER_ASC)
    {
        $this->setOrder('sort_order', $direction);
        return $this;
    }

    /**
     * Add filter by product id to collection
     *
     * @param int $productId
     *
     * @return \Magento\TargetRule\Model\Resource\Rule\Collection
     */
    public function addProductFilter($productId)
    {
        $this->getSelect()->join(
            array('product_idx' => $this->getTable('magento_targetrule_product')),
            'product_idx.rule_id = main_table.rule_id',
            array()
        )
        ->where('product_idx.product_id = ?', $productId);

        return $this;
    }
    /**
     * Add filter by segment id to collection
     *
     * @param int $segmentId
     *
     * @return \Magento\TargetRule\Model\Resource\Rule\Collection
     */
    public function addSegmentFilter($segmentId)
    {
        if (!empty($segmentId)) {
            $this->getSelect()->join(
                array('segement_idx' => $this->getTable('magento_targetrule_customersegment')),
                'segement_idx.rule_id = main_table.rule_id', array())->where('segement_idx.segment_id = ?', $segmentId);
        } else {
            $this->getSelect()->joinLeft(
                array('segement_idx' => $this->getTable('magento_targetrule_customersegment')),
                'segement_idx.rule_id = main_table.rule_id', array())->where('segement_idx.segment_id IS NULL');
        }
        return $this;
    }
}
