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
 * Rule conditions cart items subselection container
 */
class Enterprise_Reminder_Model_Rule_Condition_Cart_Subcombine
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
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Cart_Subcombine');
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
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Cart_Storeview")->getNewChildSelectOptions(),
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Cart_Sku")->getNewChildSelectOptions(),
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Cart_Attributes")->getNewChildSelectOptions()
            )
        );
    }
}
