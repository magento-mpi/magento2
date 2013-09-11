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
namespace Magento\Reminder\Model\Rule\Condition\Cart;

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
        $this->setType('\Magento\Reminder\Model\Rule\Condition\Cart\Combine');
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
                \Mage::getModel("\Magento\Reminder\Model\Rule\Condition\Cart\Couponcode")
                        ->getNewChildSelectOptions(),
                \Mage::getModel("\Magento\Reminder\Model\Rule\Condition\Cart\Itemsquantity")
                        ->getNewChildSelectOptions(),
                \Mage::getModel("\Magento\Reminder\Model\Rule\Condition\Cart\Totalquantity")
                        ->getNewChildSelectOptions(),
                \Mage::getModel("\Magento\Reminder\Model\Rule\Condition\Cart\Virtual")
                        ->getNewChildSelectOptions(),
                \Mage::getModel("\Magento\Reminder\Model\Rule\Condition\Cart\Amount")
                        ->getNewChildSelectOptions(),
                array( // subselection combo
                    'value' => '\Magento\Reminder\Model\Rule\Condition\Cart\Subselection',
                    'label' => __('Items Subselection')
                )
            )
        );
    }
}
