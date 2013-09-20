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
class Magento_CustomerSegment_Model_Segment_Condition_Order_Status
    extends Magento_CustomerSegment_Model_Condition_Abstract
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
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @var Magento_Sales_Model_Order_Config
     */
    protected $_orderConfig;

    /**
     * @param Magento_Sales_Model_Order_Config $orderConfig
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Sales_Model_Order_Config $orderConfig,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_orderConfig = $orderConfig;
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Order_Status');
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
     * @return Magento_CustomerSegment_Model_Segment_Condition_Order_Status
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array_merge(
            array(self::VALUE_ANY => __('Any')),
            $this->_orderConfig->getStatuses()
        ));
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
     * @return Magento_Eav_Model_Entity_Attribute
     */
    public function getAttributeObject()
    {
        return $this->_eavConfig->getAttribute('order', 'status');
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
