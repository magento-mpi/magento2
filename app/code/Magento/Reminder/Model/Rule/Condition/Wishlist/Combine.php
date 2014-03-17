<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Rule\Condition\Wishlist;

/**
 * Rule conditions container
 */
class Combine extends \Magento\Reminder\Model\Condition\Combine\AbstractCombine
{
    /**
     * Wishlist Sharing Factory
     *
     * @var \Magento\Reminder\Model\Rule\Condition\Wishlist\SharingFactory
     */
    protected $_sharingFactory;

    /**
     * Wishlist Quantity Factory
     *
     * @var \Magento\Reminder\Model\Rule\Condition\Wishlist\QuantityFactory
     */
    protected $_quantityFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param \Magento\Reminder\Model\Rule\Condition\Wishlist\SharingFactory $sharingFactory
     * @param \Magento\Reminder\Model\Rule\Condition\Wishlist\QuantityFactory $quantityFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        \Magento\Reminder\Model\Rule\Condition\Wishlist\SharingFactory $sharingFactory,
        \Magento\Reminder\Model\Rule\Condition\Wishlist\QuantityFactory $quantityFactory,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Wishlist\Combine');
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
            parent::getNewChildSelectOptions(),
            array(
                $this->_getRecursiveChildSelectOption(),
                $this->_sharingFactory->create()->getNewChildSelectOptions(),
                $this->_quantityFactory->create()->getNewChildSelectOptions(),
                array(
                    'value' => 'Magento\Reminder\Model\Rule\Condition\Wishlist\Subselection',
                    'label' => __('Items Subselection')
                )
            )
        );
    }
}
