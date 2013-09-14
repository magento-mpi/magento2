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
 * Order status condition
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Order;

class Status
    extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    /**
     * Any option value
     */
    const VALUE_ANY = 'any';

    /**
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Order\Status');
        $this->setValue(null);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array('sales_order_save_commit_after');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(
            'value' => $this->getType(),
            'label' => __('Order Status')
        );
    }

    /**
     * Get input type for attribute value.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Init value select options
     *
     * @return \Magento\CustomerSegment\Model\Segment\Condition\Order\Status
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array_merge(
            array(self::VALUE_ANY => __('Any')),
            \Mage::getSingleton('Magento\Sales\Model\Order\Config')->getStatuses())
        );
        return $this;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Order Status %1 %2:', $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get order status attribute object
     *
     * @return \Magento\Eav\Model\Entity\Attribute
     */
    public function getAttributeObject()
    {
        return \Mage::getSingleton('Magento\Eav\Model\Config')->getAttribute('order', 'status');
    }

    /**
     * Used subfilter type
     *
     * @return string
     */
    public function getSubfilterType()
    {
        return 'order';
    }

    /**
     * Apply status subfilter to parent/base condition query
     *
     * @param string $fieldName base query field name
     * @param bool $requireValid strict validation flag
     * @param $website
     * @return string
     */
    public function getSubfilterSql($fieldName, $requireValid, $website)
    {
        if ($this->getValue() == self::VALUE_ANY) {
            return '';
        }
        return $this->getResource()->createConditionSql($fieldName, $this->getOperator(), $this->getValue());
    }
}
