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
 * Rule conditions items subselection container
 */
namespace Magento\Reminder\Model\Rule\Condition\Wishlist;

class Subcombine
    extends \Magento\Reminder\Model\Condition\Combine\AbstractCombine
{
    /**
     * Wishlist Storeview Factory
     *
     * @var \Magento\Reminder\Model\Rule\Condition\Wishlist\StoreviewFactory
     */
    protected $_storeviewFactory;

    /**
     * Wishlist Attributes Factory
     *
     * @var \Magento\Reminder\Model\Rule\Condition\Wishlist\AttributesFactory
     */
    protected $_attrFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param \Magento\Reminder\Model\Rule\Condition\Wishlist\StoreviewFactory $storeviewFactory
     * @param \Magento\Reminder\Model\Rule\Condition\Wishlist\AttributesFactory $attrFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        \Magento\Reminder\Model\Rule\Condition\Wishlist\StoreviewFactory $storeviewFactory,
        \Magento\Reminder\Model\Rule\Condition\Wishlist\AttributesFactory $attrFactory,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Wishlist\Subcombine');
        $this->_storeviewFactory = $storeviewFactory;
        $this->_attrFactory = $attrFactory;
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
                $this->_storeviewFactory->create()->getNewChildSelectOptions(),
                $this->_attrFactory->create()->getNewChildSelectOptions()
            )
        );
    }
}
