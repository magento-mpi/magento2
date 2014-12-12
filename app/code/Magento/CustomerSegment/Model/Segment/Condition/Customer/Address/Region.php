<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Customer\Address;

use Magento\Customer\Model\Customer;
use Magento\CustomerSegment\Model\Condition\AbstractCondition;

/**
 * Customer address region selector
 */
class Region extends AbstractCondition
{
    /**
     * Input type
     *
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @var \Magento\CustomerSegment\Model\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        array $data = []
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_conditionFactory = $conditionFactory;
        parent::__construct($context, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Customer\Address\Region');
        $this->setValue(1);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        return $this->_conditionFactory->create('Customer\Address\Attributes')->getMatchedEvents();
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return [['value' => $this->getType(), 'label' => __('Has State/Province')]];
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        $element = $this->getValueElementHtml();
        return $this->getTypeElementHtml() . __(
            'If Customer Address %1 State/Province specified',
            $element
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Get element type for value select
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Init list of available values
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption(['1' => __('has'), '0' => __('does not have')]);
        return $this;
    }

    /**
     * Get condition query
     * In all cases "region name" will be in ..._varchar table
     *
     * @param Customer|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $inversion = (int)$this->getValue() ? '' : ' NOT ';
        $attribute = $this->_eavConfig->getAttribute('customer_address', 'region');
        $select = $this->getResource()->createSelect();
        $ifNull = $this->getResource()->getReadConnection()->getCheckSql("caev.value IS {$inversion} NULL", 0, 1);
        $select->from(['caev' => $attribute->getBackendTable()], "({$ifNull})");
        $select->where(
            'caev.attribute_id = ?',
            $attribute->getId()
        )->where(
            "caev.entity_id = customer_address.entity_id"
        );
        $select->limit(1);
        return $select;
    }
}
