<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Segment conditions container
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerSegment_Model_Segment_Condition_Combine
    extends Magento_CustomerSegment_Model_Condition_Combine_Abstract
{
    /**
     * Initialize model
     *
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array(
                // Subconditions combo
                'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Combine',
                'label' => __('Conditions Combination'),
                'available_in_guest_mode' => true
            ),
            array(
                // Customer address combo
                'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Customer_Address',
                'label' => __('Customer Address')
            ),
            // Customer attribute group
            Mage::getModel('Magento_CustomerSegment_Model_Segment_Condition_Customer')
                ->getNewChildSelectOptions(),

            // Shopping cart group
            Mage::getModel('Magento_CustomerSegment_Model_Segment_Condition_Shoppingcart')
                ->getNewChildSelectOptions(),

            array(
                'value' => array(
                    array(
                        // Product list combo
                        'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Product_Combine_List',
                        'label' => __('Product List'),
                        'available_in_guest_mode' => true
                    ),
                    array(
                        // Product history combo
                        'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Product_Combine_History',
                        'label' => __('Product History'),
                        'available_in_guest_mode' => true
                    )
                ),
                'label' => __('Products'),
                'available_in_guest_mode' => true
            ),

            // Sales group
            Mage::getModel('Magento_CustomerSegment_Model_Segment_Condition_Sales')->getNewChildSelectOptions(),
        );
        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $this->_prepareConditionAccordingApplyToValue($conditions);
    }

    /**
     * Prepare base condition select which related with current condition combine
     *
     * @param $customer
     * @param $website
     * @return \Magento\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = parent::_prepareConditionsSql($customer, $website);
        $select->limit(1);
        return $select;
    }

    /**
     * Prepare Condition According to ApplyTo Value
     *
     * @param array $conditions
     * @return array
     */
    protected function _prepareConditionAccordingApplyToValue(array $conditions)
    {
        $returnedConditions = null;
        switch ($this->getRule()->getApplyTo()) {
            case Magento_CustomerSegment_Model_Segment::APPLY_TO_VISITORS:
                $returnedConditions = $this->_removeUnnecessaryConditions($conditions);
                break;

            case Magento_CustomerSegment_Model_Segment::APPLY_TO_VISITORS_AND_REGISTERED:
                $returnedConditions = $this->_markConditions($conditions);
                break;

            case Magento_CustomerSegment_Model_Segment::APPLY_TO_REGISTERED:
                $returnedConditions = $conditions;
                break;

            default:
                Mage::throwException(
                    __('Wrong "ApplyTo" type')
                );
                break;
        }
        return $returnedConditions;
    }


    /**
     * Remove unnecessary conditions
     *
     * @param array $conditionsList
     * @return array
     */
    protected function _removeUnnecessaryConditions(array $conditionsList)
    {
        $conditionResult = $conditionsList;
        foreach ($conditionResult as $key => $condition) {
            if ($key == 0 && isset($condition['value']) && $condition['value'] == '') {
                continue;
            }
            if (array_key_exists('available_in_guest_mode', $condition) && $condition['available_in_guest_mode']) {
                if (is_array($conditionResult[$key]['value'])) {
                    $conditionResult[$key]['value'] = $this->_removeUnnecessaryConditions($condition['value']);
                }
            } else {
                unset($conditionResult[$key]);
            }
        }
        return $conditionResult;
    }


    /**
     * Mark condition with asterisk
     *
     * @param array $conditionsList
     * @return array
     */
    protected function _markConditions(array $conditionsList)
    {
        $conditionResult = $conditionsList;
        foreach ($conditionResult as $key => $condition) {
            if (array_key_exists('available_in_guest_mode', $condition) && $condition['available_in_guest_mode']) {
                $conditionResult[$key]['label'] .= '*';
                if (is_array($conditionResult[$key]['value'])) {
                    $conditionResult[$key]['value'] = $this->_markConditions($condition['value']);
                }
            }
        }
        return $conditionResult;
    }
}
