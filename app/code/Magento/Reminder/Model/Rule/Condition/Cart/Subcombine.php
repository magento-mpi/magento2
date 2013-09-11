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
 * Rule conditions cart items subselection container
 */
namespace Magento\Reminder\Model\Rule\Condition\Cart;

class Subcombine
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
        $this->setType('\Magento\Reminder\Model\Rule\Condition\Cart\Subcombine');
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
                \Mage::getModel("\Magento\Reminder\Model\Rule\Condition\Cart\Storeview")->getNewChildSelectOptions(),
                \Mage::getModel("\Magento\Reminder\Model\Rule\Condition\Cart\Sku")->getNewChildSelectOptions(),
                \Mage::getModel("\Magento\Reminder\Model\Rule\Condition\Cart\Attributes")->getNewChildSelectOptions()
            )
        );
    }
}
