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
     * Wishlist Sharing Factory
     *
     * @var Magento_Reminder_Model_Rule_Condition_Wishlist_SharingFactory
     */
    protected $_sharingFactory;

    /**
     * Wishlist Quantity Factory
     *
     * @var Magento_Reminder_Model_Rule_Condition_Wishlist_QuantityFactory
     */
    protected $_quantityFactory;

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_Reminder_Model_Resource_Rule $ruleResource
     * @param Magento_Reminder_Model_Rule_Condition_Wishlist_SharingFactory $sharingFactory
     * @param Magento_Reminder_Model_Rule_Condition_Wishlist_QuantityFactory $quantityFactory
     * @param array $data
     */
    public function __construct(
        Magento_Rule_Model_Condition_Context $context,
        Magento_Reminder_Model_Resource_Rule $ruleResource,
        Magento_Reminder_Model_Rule_Condition_Wishlist_SharingFactory $sharingFactory,
        Magento_Reminder_Model_Rule_Condition_Wishlist_QuantityFactory $quantityFactory,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento_Reminder_Model_Rule_Condition_Wishlist_Combine');
        $this->_sharingFactory = $sharingFactory;
        $this->_quantityFactory = $quantityFactory;
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
                $this->_sharingFactory->create()->getNewChildSelectOptions(),
                $this->_quantityFactory->create()->getNewChildSelectOptions(),
                array( // subselection combo
                    'value' => 'Magento_Reminder_Model_Rule_Condition_Wishlist_Subselection',
                    'label' => __('Items Subselection')
                )
            )
        );
    }
}
