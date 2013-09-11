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
namespace Magento\CustomerSegment\Model\Segment\Condition\Customer\Address;

class Region
    extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    /**
     * Input type
     *
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
        $this->setType('\Magento\CustomerSegment\Model\Segment\Condition\Customer\Address\Region');
        $this->setValue(1);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return \Mage::getModel('\Magento\CustomerSegment\Model\Segment\Condition\Customer\Address\Attributes')
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
     * @return \Magento\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $inversion = ((int)$this->getValue() ? '' : ' NOT ');
        $attribute = \Mage::getSingleton('Magento\Eav\Model\Config')->getAttribute('customer_address', 'region');
        $select = $this->getResource()->createSelect();
        $ifNull = $this->getResource()->getReadConnection()->getCheckSql("caev.value IS {$inversion} NULL", 0, 1);
        $select->from(array('caev' => $attribute->getBackendTable()), "({$ifNull})");
        $select->where('caev.attribute_id = ?', $attribute->getId())
            ->where("caev.entity_id = customer_address.entity_id");
        $select->limit(1);
        return $select;
    }
}
