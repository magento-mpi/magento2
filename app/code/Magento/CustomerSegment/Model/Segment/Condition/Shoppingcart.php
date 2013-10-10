<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart conditions options group
 */
namespace Magento\CustomerSegment\Model\Segment\Condition;

class Shoppingcart
    extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\CustomerSegment\Model\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        \Magento\Rule\Model\Condition\Context $context,
        array $data = array()
    ) {
        $this->_conditionFactory = $conditionFactory;
        parent::__construct($resourceSegment, $context, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart');
        $this->setValue(null);
    }

    /**
     * Get condition "selectors" for parent block
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(
            'value' => array(
                $this->_conditionFactory->create('Shoppingcart\Amount')->getNewChildSelectOptions(),
                $this->_conditionFactory->create('Shoppingcart\Itemsquantity')->getNewChildSelectOptions(),
                $this->_conditionFactory->create('Shoppingcart\Productsquantity')->getNewChildSelectOptions(),
            ),
            'label' => __('Shopping Cart'),
            'available_in_guest_mode' => true,
        );
    }
}
