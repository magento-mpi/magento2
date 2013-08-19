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
class Enterprise_Reminder_Model_Rule_Condition_Cart_Combine
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
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Cart_Combine');
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
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Cart_Couponcode")
                        ->getNewChildSelectOptions(),
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Cart_Itemsquantity")
                        ->getNewChildSelectOptions(),
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Cart_Totalquantity")
                        ->getNewChildSelectOptions(),
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Cart_Virtual")
                        ->getNewChildSelectOptions(),
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Cart_Amount")
                        ->getNewChildSelectOptions(),
                array( // subselection combo
                    'value' => 'Enterprise_Reminder_Model_Rule_Condition_Cart_Subselection',
                    'label' => __('Items Subselection')
                )
            )
        );
    }
}
