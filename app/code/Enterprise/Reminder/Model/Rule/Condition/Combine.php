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
     * @param Mage_Rule_Model_Condition_Context $context
     */
    public function __construct(Mage_Rule_Model_Condition_Context $context)
    {
        parent::__construct($context);
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
                'label' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Wishlist')),

            array( // customer shopping cart combo
                'value' => 'Enterprise_Reminder_Model_Rule_Condition_Cart',
                'label' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Shopping Cart')),

        );

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }
}
