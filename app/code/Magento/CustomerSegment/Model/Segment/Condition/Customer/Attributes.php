<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Customer;

use Magento\Customer\Model\Customer;
use Magento\CustomerSegment\Model\Condition\AbstractCondition;

/**
 * Customer attributes condition
 */
class Attributes extends AbstractCondition
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Customer\Model\Resource\Customer
     */
    protected $_resourceCustomer;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param \Magento\Customer\Model\Resource\Customer $resourceCustomer
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        \Magento\Customer\Model\Resource\Customer $resourceCustomer,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        $this->_resourceCustomer = $resourceCustomer;
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Customer\Attributes');
        $this->setValue(null);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        return ['customer_save_commit_after'];
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = [];
        foreach ($attributes as $code => $label) {
            $conditions[] = ['value' => $this->getType() . '|' . $code, 'label' => $label];
        }

        return $conditions;
    }

    /**
     * Retrieve attribute object
     *
     * @return \Magento\Eav\Model\Entity\Attribute
     */
    public function getAttributeObject()
    {
        return $this->_eavConfig->getAttribute('customer', $this->getAttribute());
    }

    /**
     * Load condition options for castomer attributes
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $productAttributes = $this->_resourceCustomer->loadAllAttributes()->getAttributesByCode();

        $attributes = [];

        foreach ($productAttributes as $attribute) {
            $label = $attribute->getFrontendLabel();
            if (!$label) {
                continue;
            }
            // skip "binary" attributes
            if (in_array($attribute->getFrontendInput(), ['file', 'image'])) {
                continue;
            }
            if ($attribute->getIsUsedForCustomerSegment()) {
                $attributes[$attribute->getAttributeCode()] = $label;
            }
        }
        asort($attributes);
        $this->setAttributeOption($attributes);
        return $this;
    }

    /**
     * Retrieve select option values
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        if (!$this->getData('value_select_options') && is_object($this->getAttributeObject())) {
            if ($this->getAttributeObject()->usesSource()) {
                if ($this->getAttributeObject()->getFrontendInput() == 'multiselect') {
                    $addEmptyOption = false;
                } else {
                    $addEmptyOption = true;
                }
                $optionsArr = $this->getAttributeObject()->getSource()->getAllOptions($addEmptyOption);
                $this->setData('value_select_options', $optionsArr);
            }

            if ($this->_isCurrentAttributeDefaultAddress()) {
                $optionsArr = $this->_getOptionsForAttributeDefaultAddress();
                $this->setData('value_select_options', $optionsArr);
            }
        }

        return $this->getData('value_select_options');
    }

    /**
     * Get input type for attribute operators.
     *
     * @return string
     */
    public function getInputType()
    {
        if ($this->_isCurrentAttributeDefaultAddress()) {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }
        $input = $this->getAttributeObject()->getFrontendInput();
        switch ($input) {
            case 'boolean':
                return 'select';
            case 'select':
            case 'multiselect':
            case 'date':
                return $input;
            default:
                return 'string';
        }
    }

    /**
     * Get attribute value input element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        if ($this->_isCurrentAttributeDefaultAddress()) {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }
        $input = $this->getAttributeObject()->getFrontendInput();
        switch ($input) {
            case 'boolean':
                return 'select';
            case 'select':
            case 'multiselect':
            case 'date':
                return $input;
            default:
                return 'text';
        }
    }

    /**
     * Retrieve value element
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    $element->setImage($this->_assetRepo->getUrl('images/grid-cal.gif'));
                    break;
            }
        }
        return $element;
    }

    /**
     * Check if attribute value should be explicit
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    return true;
            }
        }
        return false;
    }

    /**
     * Retrieve attribute element
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * Get attribute operator html
     *
     * @return string
     */
    public function getOperatorElementHtml()
    {
        if ($this->_isCurrentAttributeDefaultAddress()) {
            return '';
        }
        return parent::getOperatorElementHtml();
    }

    /**
     * Check if current condition attribute is default billing or shipping address
     *
     * @return bool
     */
    protected function _isCurrentAttributeDefaultAddress()
    {
        $code = $this->getAttributeObject()->getAttributeCode();
        return $code == 'default_billing' || $code == 'default_shipping';
    }

    /**
     * Get options for customer default address attributes value select
     *
     * @return array
     */
    protected function _getOptionsForAttributeDefaultAddress()
    {
        return [
            ['value' => 'is_exists', 'label' => __('exists')],
            ['value' => 'is_not_exists', 'label' => __('does not exist')]
        ];
    }

    /**
     * Customer attributes are standalone conditions, hence they must be self-sufficient
     *
     * @return string
     */
    public function asHtml()
    {
        return __('Customer %1', parent::asHtml());
    }

    /**
     * Return values of start and end datetime for date if operator is equal
     *
     * @return array
     */
    public function getDateValue()
    {
        if ($this->getOperator() == '==') {
            $dateObj = $this->_localeDate->date(
                $this->getValue(),
                \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                null,
                false
            )->setHour(
                0
            )->setMinute(
                0
            )->setSecond(
                0
            );
            $value = [
                'start' => $dateObj->toString(\Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT),
                'end' => $dateObj->addDay(1)->toString(\Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT),
            ];
            return $value;
        }
        return $this->getValue();
    }

    /**
     * Return date operator if original operator is equal
     *
     * @return string
     */
    public function getDateOperator()
    {
        if ($this->getOperator() == '==') {
            return 'between';
        }
        return $this->getOperator();
    }

    /**
     * Create SQL condition select for customer attribute
     *
     * @param Customer|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $attribute = $this->getAttributeObject();
        $table = $attribute->getBackendTable();
        $select = $this->getResource()->createSelect();
        $select->from(['main' => $table], [new \Zend_Db_Expr(1)]);
        $select->where($this->_createCustomerFilter($customer, 'main.entity_id'));
        $select->limit(1);

        if (!in_array($attribute->getAttributeCode(), ['default_billing', 'default_shipping'])) {
            $value = $this->getValue();
            $operator = $this->getOperator();
            if ($attribute->isStatic()) {
                $field = "main.{$attribute->getAttributeCode()}";
            } else {
                $select->where('main.attribute_id = ?', $attribute->getId());
                $field = 'main.value';
            }
            $field = $select->getAdapter()->quoteColumnAs($field, null);

            if ($attribute->getFrontendInput() == 'date') {
                $value = $this->getDateValue();
                $operator = $this->getDateOperator();
            }
            $condition = $this->getResource()->createConditionSql($field, $operator, $value);
            $select->where($condition);
        } else {
            if ($this->getValue() == 'is_exists') {
                $ifCondition = 'COUNT(*) != 0';
            } else {
                $ifCondition = 'COUNT(*) = 0';
            }
            $select->reset(\Zend_Db_Select::COLUMNS);
            $condition = $this->getResource()->getReadConnection()->getCheckSql($ifCondition, '1', '0');
            $select->columns(new \Zend_Db_Expr($condition));
            $select->where('main.attribute_id = ?', $attribute->getId());
        }
        return $select;
    }
}
