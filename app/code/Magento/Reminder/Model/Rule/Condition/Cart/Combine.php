<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Rule\Condition\Cart;

/**
 * Rule conditions container
 */
class Combine extends \Magento\Reminder\Model\Condition\Combine\AbstractCombine
{
    /**
     * Cart Couponcode Factory
     *
     * @var \Magento\Reminder\Model\Rule\Condition\Cart\CouponcodeFactory
     */
    protected $_couponFactory;

    /**
     * Cart Items Quantity Factory
     *
     * @var \Magento\Reminder\Model\Rule\Condition\Cart\ItemsquantityFactory
     */
    protected $_itemsQtyFactory;

    /**
     * Total Quantity Factory
     *
     * @var \Magento\Reminder\Model\Rule\Condition\Cart\TotalquantityFactory
     */
    protected $_totalQtyFactory;

    /**
     * Cart Virtual Factory
     *
     * @var \Magento\Reminder\Model\Rule\Condition\Cart\VirtualFactory
     */
    protected $_virtualFactory;

    /**
     * Cart Amount Factory
     *
     * @var \Magento\Reminder\Model\Rule\Condition\Cart\AmountFactory
     */
    protected $_amountFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param \Magento\Reminder\Model\Rule\Condition\Cart\CouponcodeFactory $couponFactory
     * @param \Magento\Reminder\Model\Rule\Condition\Cart\ItemsquantityFactory $itemsQtyFactory
     * @param \Magento\Reminder\Model\Rule\Condition\Cart\TotalquantityFactory $totalQtyFactory
     * @param \Magento\Reminder\Model\Rule\Condition\Cart\VirtualFactory $virtualFactory
     * @param \Magento\Reminder\Model\Rule\Condition\Cart\AmountFactory $amountFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        \Magento\Reminder\Model\Rule\Condition\Cart\CouponcodeFactory $couponFactory,
        \Magento\Reminder\Model\Rule\Condition\Cart\ItemsquantityFactory $itemsQtyFactory,
        \Magento\Reminder\Model\Rule\Condition\Cart\TotalquantityFactory $totalQtyFactory,
        \Magento\Reminder\Model\Rule\Condition\Cart\VirtualFactory $virtualFactory,
        \Magento\Reminder\Model\Rule\Condition\Cart\AmountFactory $amountFactory,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Cart\Combine');
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
            parent::getNewChildSelectOptions(),
            array(
                $this->_getRecursiveChildSelectOption(),
                $this->_couponFactory->create()->getNewChildSelectOptions(),
                $this->_itemsQtyFactory->create()->getNewChildSelectOptions(),
                $this->_totalQtyFactory->create()->getNewChildSelectOptions(),
                $this->_virtualFactory->create()->getNewChildSelectOptions(),
                $this->_amountFactory->create()->getNewChildSelectOptions(),
                array(
                    'value' => 'Magento\Reminder\Model\Rule\Condition\Cart\Subselection',
                    'label' => __('Items Subselection')
                )
            )
        );
    }
}
