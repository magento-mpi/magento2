<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rule conditions container
 */
class Magento_Reminder_Model_Rule_Condition_Combine
    extends Magento_Reminder_Model_Condition_Combine_Abstract
{
    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_Reminder_Model_Resource_Rule $ruleResource
     * @param array $data
     */
    public function __construct(
        Magento_Rule_Model_Condition_Context $context,
        Magento_Reminder_Model_Resource_Rule $ruleResource,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento_Reminder_Model_Rule_Condition_Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array( // customer wishlist combo
                'value' => 'Magento_Reminder_Model_Rule_Condition_Wishlist',
                'label' => __('Wish List')),

            array( // customer shopping cart combo
                'value' => 'Magento_Reminder_Model_Rule_Condition_Cart',
                'label' => __('Shopping Cart')),

        );

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }
}
