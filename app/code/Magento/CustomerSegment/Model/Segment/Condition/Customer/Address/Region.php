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
 * Customer address region selector
 *
 */
class Magento_CustomerSegment_Model_Segment_Condition_Customer_Address_Region
    extends Magento_CustomerSegment_Model_Condition_Abstract
{
    /**
     * Input type
     *
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @var Magento_CustomerSegment_Model_ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @param Magento_CustomerSegment_Model_Resource_Segment $resourceSegment
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_CustomerSegment_Model_ConditionFactory $conditionFactory
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerSegment_Model_Resource_Segment $resourceSegment,
        Magento_Eav_Model_Config $eavConfig,
        Magento_CustomerSegment_Model_ConditionFactory $conditionFactory,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_conditionFactory = $conditionFactory;
        parent::__construct($resourceSegment, $context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Customer_Address_Region');
        $this->setValue(1);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return $this->_conditionFactory->create('Customer_Address_Attributes')
            ->getMatchedEvents();
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(array(
            'value' => $this->getType(),
            'label' => __('Has State/Province')
        ));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        $element = $this->getValueElementHtml();
        return $this->getTypeElementHtml() . __('If Customer Address %1 State/Province specified', $element)
            . $this->getRemoveLinkHtml();
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
     * @return array
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            '1' => __('has'),
            '0' => __('does not have'),
        ));
        return $this;
    }

    /**
     * Get condition query
     * In all cases "region name" will be in ..._varchar table
     *
     * @param $customer
     * @param $website
     * @return Magento_DB_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $inversion = ((int)$this->getValue() ? '' : ' NOT ');
        $attribute = $this->_eavConfig->getAttribute('customer_address', 'region');
        $select = $this->getResource()->createSelect();
        $ifNull = $this->getResource()->getReadConnection()->getCheckSql("caev.value IS {$inversion} NULL", 0, 1);
        $select->from(array('caev' => $attribute->getBackendTable()), "({$ifNull})");
        $select->where('caev.attribute_id = ?', $attribute->getId())
            ->where("caev.entity_id = customer_address.entity_id");
        $select->limit(1);
        return $select;
    }
}
