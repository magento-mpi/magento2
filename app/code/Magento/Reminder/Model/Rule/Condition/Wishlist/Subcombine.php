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
 * Rule conditions items subselection container
 */
class Magento_Reminder_Model_Rule_Condition_Wishlist_Subcombine
    extends Magento_Reminder_Model_Condition_Combine_Abstract
{
    /**
     * Wishlist Storeview Factory
     *
     * @var Magento_Reminder_Model_Rule_Condition_Wishlist_StoreviewFactory
     */
    protected $_storeviewFactory;

    /**
     * Wishlist Attributes Factory
     *
     * @var Magento_Reminder_Model_Rule_Condition_Wishlist_AttributesFactory
     */
    protected $_attrFactory;

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_Reminder_Model_Resource_Rule $ruleResource
     * @param Magento_Reminder_Model_Rule_Condition_Wishlist_StoreviewFactory $storeviewFactory
     * @param Magento_Reminder_Model_Rule_Condition_Wishlist_AttributesFactory $attrFactory
     * @param array $data
     */
    public function __construct(
        Magento_Rule_Model_Condition_Context $context,
        Magento_Reminder_Model_Resource_Rule $ruleResource,
        Magento_Reminder_Model_Rule_Condition_Wishlist_StoreviewFactory $storeviewFactory,
        Magento_Reminder_Model_Rule_Condition_Wishlist_AttributesFactory $attrFactory,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento_Reminder_Model_Rule_Condition_Wishlist_Subcombine');
        $this->_storeviewFactory = $storeviewFactory;
        $this->_attrFactory = $attrFactory;
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
                $this->_storeviewFactory->create()->getNewChildSelectOptions(),
                $this->_attrFactory->create()->getNewChildSelectOptions()
            )
        );
    }
}
