<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Rule\Condition;

/**
 * Rule conditions container
 */
class Combine extends \Magento\Reminder\Model\Condition\Combine\AbstractCombine
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array( // customer wishlist combo
                'value' => 'Magento\Reminder\Model\Rule\Condition\Wishlist',
                'label' => __('Wish List')),

            array( // customer shopping cart combo
                'value' => 'Magento\Reminder\Model\Rule\Condition\Cart',
                'label' => __('Shopping Cart')),

        );

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }
}
