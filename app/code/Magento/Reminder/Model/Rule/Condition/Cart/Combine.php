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
class Magento_Reminder_Model_Rule_Condition_Cart_Combine
    extends Magento_Reminder_Model_Condition_Combine_Abstract
{
    /**
     * Cart Couponcode Factory
     *
     * @var Magento_Reminder_Model_Rule_Condition_Cart_CouponcodeFactory
     */
    protected $_couponFactory;

    /**
     * Cart Items Quantity Factory
     *
     * @var Magento_Reminder_Model_Rule_Condition_Cart_ItemsquantityFactory
     */
    protected $_itemsQtyFactory;

    /**
     * Total Quantity Factory
     *
     * @var Magento_Reminder_Model_Rule_Condition_Cart_TotalquantityFactory
     */
    protected $_totalQtyFactory;

    /**
     * Cart Virtual Factory
     *
     * @var Magento_Reminder_Model_Rule_Condition_Cart_VirtualFactory
     */
    protected $_virtualFactory;

    /**
     * Cart Amount Factory
     *
     * @var Magento_Reminder_Model_Rule_Condition_Cart_AmountFactory
     */
    protected $_amountFactory;

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_Reminder_Model_Resource_Rule $ruleResource
     * @param Magento_Reminder_Model_Rule_Condition_Cart_CouponcodeFactory $couponFactory
     * @param Magento_Reminder_Model_Rule_Condition_Cart_ItemsquantityFactory $itemsQtyFactory
     * @param Magento_Reminder_Model_Rule_Condition_Cart_TotalquantityFactory $totalQtyFactory
     * @param Magento_Reminder_Model_Rule_Condition_Cart_VirtualFactory $virtualFactory
     * @param Magento_Reminder_Model_Rule_Condition_Cart_AmountFactory $amountFactory
     * @param array $data
     */
    public function __construct(
        Magento_Rule_Model_Condition_Context $context,
        Magento_Reminder_Model_Resource_Rule $ruleResource,
        Magento_Reminder_Model_Rule_Condition_Cart_CouponcodeFactory $couponFactory,
        Magento_Reminder_Model_Rule_Condition_Cart_ItemsquantityFactory $itemsQtyFactory,
        Magento_Reminder_Model_Rule_Condition_Cart_TotalquantityFactory $totalQtyFactory,
        Magento_Reminder_Model_Rule_Condition_Cart_VirtualFactory $virtualFactory,
        Magento_Reminder_Model_Rule_Condition_Cart_AmountFactory $amountFactory,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento_Reminder_Model_Rule_Condition_Cart_Combine');
        $this->_couponFactory = $couponFactory;
        $this->_itemsQtyFactory = $itemsQtyFactory;
        $this->_totalQtyFactory = $totalQtyFactory;
        $this->_virtualFactory = $virtualFactory;
        $this->_amountFactory = $amountFactory;
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
                $this->_couponFactory->create()->getNewChildSelectOptions(),
                $this->_itemsQtyFactory->create()->getNewChildSelectOptions(),
                $this->_totalQtyFactory->create()->getNewChildSelectOptions(),
                $this->_virtualFactory->create()->getNewChildSelectOptions(),
                $this->_amountFactory->create()->getNewChildSelectOptions(),
                array( // subselection combo
                    'value' => 'Magento_Reminder_Model_Rule_Condition_Cart_Subselection',
                    'label' => __('Items Subselection')
                )
            )
        );
    }
}
