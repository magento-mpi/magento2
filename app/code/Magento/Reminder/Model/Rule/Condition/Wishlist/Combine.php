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
class Magento_Reminder_Model_Rule_Condition_Wishlist_Combine
    extends Magento_Reminder_Model_Condition_Combine_Abstract
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
        $this->setType('Magento_Reminder_Model_Rule_Condition_Wishlist_Combine');
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
                Mage::getModel("Magento_Reminder_Model_Rule_Condition_Wishlist_Sharing")
                    ->getNewChildSelectOptions(),
                Mage::getModel("Magento_Reminder_Model_Rule_Condition_Wishlist_Quantity")
                    ->getNewChildSelectOptions(),
                array( // subselection combo
                    'value' => 'Magento_Reminder_Model_Rule_Condition_Wishlist_Subselection',
                    'label' => __('Items Subselection')
                )
            )
        );
    }
}
