<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attributes condition combine
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine
    extends Enterprise_CustomerSegment_Model_Condition_Combine_Abstract
{
    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $children = array_merge_recursive(
            parent::getNewChildSelectOptions(),
            array(
                array( // self
                    'value' => $this->getType(),
                    'label' => __('Conditions Combination')
                )
            )
        );

        if ($this->getDateConditions()) {
            $children = array_merge_recursive(
                $children,
                array(
                    array(
                        'value' => array(
                            Mage::getModel('Enterprise_CustomerSegment_Model_Segment_Condition_Uptodate')->getNewChildSelectOptions(),
                            Mage::getModel('Enterprise_CustomerSegment_Model_Segment_Condition_Daterange')->getNewChildSelectOptions(),
                        ),
                        'label' => __('Date Ranges')
                    )
                )
            );
        }

        $children = array_merge_recursive(
            $children,
            array(
                Mage::getModel('Enterprise_CustomerSegment_Model_Segment_Condition_Product_Attributes')->getNewChildSelectOptions()
            )
        );

        return $children;
    }

    /**
     * Combine not present his own SQL condition
     *
     * @param $customer
     * @param $website
     * @return false
     */
    public function getConditionsSql($customer, $website)
    {
        return false;
    }

    /**
     * Get combine subfilter type
     *
     * @return string
     */
    public function getSubfilterType()
    {
        return 'product';
    }

    /**
     * Apply product attribute subfilter to parent/base condition query
     *
     * @param string $fieldName base query field name
     * @param bool $requireValid strict validation flag
     * @param $website
     * @return string
     */
    public function getSubfilterSql($fieldName, $requireValid, $website)
    {
        $table = $this->getResource()->getTable('catalog_product_entity');

        $select = $this->getResource()->createSelect();
        $select->from(array('main'=>$table), array('entity_id'));

        if ($this->getAggregator() == 'all') {
            $whereFunction = 'where';
        } else {
            $whereFunction = 'orWhere';
        }

        $gotConditions = false;
        foreach ($this->getConditions() as $condition) {
            if ($condition->getSubfilterType() == 'product') {
                $subfilter = $condition->getSubfilterSql('main.entity_id', ($this->getValue() == 1), $website);
                if ($subfilter) {
                    $select->$whereFunction($subfilter);
                    $gotConditions = true;
                }
            }
        }
        if (!$gotConditions) {
            $select->where('1=1');
        }

        $inOperator = ($requireValid ? 'IN' : 'NOT IN');
        return sprintf("%s %s (%s)", $fieldName, $inOperator, $select);
    }
}
