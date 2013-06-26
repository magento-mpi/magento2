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
class Enterprise_Reminder_Model_Rule_Condition_Wishlist_Combine
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
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Wishlist_Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array_merge_recursive(
            parent::getNewChildSelectOptions(), array(
                $this->_getRecursiveChildSelectOption(),
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Wishlist_Sharing")->getNewChildSelectOptions(),
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Wishlist_Quantity")->getNewChildSelectOptions(),
                array( // subselection combo
                    'value' => 'Enterprise_Reminder_Model_Rule_Condition_Wishlist_Subselection',
                    'label' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Items Subselection')
                )
            )
        );
    }
}
