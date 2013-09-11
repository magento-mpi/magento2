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
namespace Magento\Reminder\Model\Rule\Condition;

class Combine
    extends \Magento\Reminder\Model\Condition\Combine\AbstractCombine
{
    /**
     * Initialize model
     *
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('\Magento\Reminder\Model\Rule\Condition\Combine');
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
                'value' => '\Magento\Reminder\Model\Rule\Condition\Wishlist',
                'label' => __('Wish List')),

            array( // customer shopping cart combo
                'value' => '\Magento\Reminder\Model\Rule\Condition\Cart',
                'label' => __('Shopping Cart')),

        );

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }
}
