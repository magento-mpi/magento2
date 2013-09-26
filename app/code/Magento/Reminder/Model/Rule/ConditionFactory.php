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
 * Factory class for Rule Condition
 */
class Magento_Reminder_Model_Rule_ConditionFactory
{
    /**
     * Object Manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Available conditions
     *
     * @var array
     */
    protected $_conditions = array(
        'Magento_Reminder_Model_Rule_Condition_Cart_Amount',
        'Magento_Reminder_Model_Rule_Condition_Cart_Attributes',
        'Magento_Reminder_Model_Rule_Condition_Cart_Combine',
        'Magento_Reminder_Model_Rule_Condition_Cart_Couponcode',
        'Magento_Reminder_Model_Rule_Condition_Cart_Itemsquantity',
        'Magento_Reminder_Model_Rule_Condition_Cart_Sku',
        'Magento_Reminder_Model_Rule_Condition_Cart_Storeview',
        'Magento_Reminder_Model_Rule_Condition_Cart_Subcombine',
        'Magento_Reminder_Model_Rule_Condition_Cart_Subselection',
        'Magento_Reminder_Model_Rule_Condition_Cart_Totalquantity',
        'Magento_Reminder_Model_Rule_Condition_Cart_Virtual',
        'Magento_Reminder_Model_Rule_Condition_Combine_Root',
        'Magento_Reminder_Model_Rule_Condition_Wishlist_Attributes',
        'Magento_Reminder_Model_Rule_Condition_Wishlist_Combine',
        'Magento_Reminder_Model_Rule_Condition_Wishlist_Quantity',
        'Magento_Reminder_Model_Rule_Condition_Wishlist_Sharing',
        'Magento_Reminder_Model_Rule_Condition_Wishlist_Storeview',
        'Magento_Reminder_Model_Rule_Condition_Wishlist_Subcombine',
        'Magento_Reminder_Model_Rule_Condition_Wishlist_Subselection',
        'Magento_Reminder_Model_Rule_Condition_Cart',
        'Magento_Reminder_Model_Rule_Condition_Combine',
        'Magento_Reminder_Model_Rule_Condition_Wishlist',
    );

    /**
     * Factory constructor
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $type
     * @return Magento_Rule_Model_Condition_Abstract
     * @throws InvalidArgumentException
     */
    public function create($type)
    {
        if (in_array($type, $this->_conditions)) {
            return $this->_objectManager->create($type);
        } else {
            throw new InvalidArgumentException(__('Condition type is unexpected'));
        }
    }
}