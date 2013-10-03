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
namespace Magento\Reminder\Model\Rule;

class ConditionFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Available conditions
     *
     * @var array
     */
    protected $_conditions = array(
        'Magento\Reminder\Model\Rule\Condition\Cart\Amount',
        'Magento\Reminder\Model\Rule\Condition\Cart\Attributes',
        'Magento\Reminder\Model\Rule\Condition\Cart\Combine',
        'Magento\Reminder\Model\Rule\Condition\Cart\Couponcode',
        'Magento\Reminder\Model\Rule\Condition\Cart\Itemsquantity',
        'Magento\Reminder\Model\Rule\Condition\Cart\Sku',
        'Magento\Reminder\Model\Rule\Condition\Cart\Storeview',
        'Magento\Reminder\Model\Rule\Condition\Cart\Subcombine',
        'Magento\Reminder\Model\Rule\Condition\Cart\Subselection',
        'Magento\Reminder\Model\Rule\Condition\Cart\Totalquantity',
        'Magento\Reminder\Model\Rule\Condition\Cart\Virtual',
        'Magento\Reminder\Model\Rule\Condition\Combine\Root',
        'Magento\Reminder\Model\Rule\Condition\Wishlist\Attributes',
        'Magento\Reminder\Model\Rule\Condition\Wishlist\Combine',
        'Magento\Reminder\Model\Rule\Condition\Wishlist\Quantity',
        'Magento\Reminder\Model\Rule\Condition\Wishlist\Sharing',
        'Magento\Reminder\Model\Rule\Condition\Wishlist\Storeview',
        'Magento\Reminder\Model\Rule\Condition\Wishlist\Subcombine',
        'Magento\Reminder\Model\Rule\Condition\Wishlist\Subselection',
        'Magento\Reminder\Model\Rule\Condition\Cart',
        'Magento\Reminder\Model\Rule\Condition\Combine',
        'Magento\Reminder\Model\Rule\Condition\Wishlist',
    );

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $type
     * @return \Magento\Rule\Model\Condition\AbstractCondition
     * @throws \InvalidArgumentException
     */
    public function create($type)
    {
        if (in_array($type, $this->_conditions)) {
            return $this->_objectManager->create($type);
        } else {
            throw new \InvalidArgumentException(__('Condition type is unexpected'));
        }
    }
}