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
 * Rule conditions items subselection container
 */
class Enterprise_Reminder_Model_Rule_Condition_Wishlist_Subcombine
    extends Enterprise_Reminder_Model_Condition_Combine_Abstract
{
    /**
     * Intialize model
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Wishlist_Subcombine');
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
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Wishlist_Storeview")->getNewChildSelectOptions(),
                Mage::getModel("Enterprise_Reminder_Model_Rule_Condition_Wishlist_Attributes")->getNewChildSelectOptions()
            )
        );
    }
}
