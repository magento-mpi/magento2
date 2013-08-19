<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rule conditions container
 */
class Enterprise_Reminder_Model_Rule_Condition_Combine
    extends Enterprise_Reminder_Model_Condition_Combine_Abstract
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
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Combine');
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
                'value' => 'Enterprise_Reminder_Model_Rule_Condition_Wishlist',
                'label' => __('Wish List')),

            array( // customer shopping cart combo
                'value' => 'Enterprise_Reminder_Model_Rule_Condition_Cart',
                'label' => __('Shopping Cart')),

        );

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }
}
