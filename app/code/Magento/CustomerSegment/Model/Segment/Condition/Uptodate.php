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
 * Period "Last N Days" condition class
 */
namespace Magento\CustomerSegment\Model\Segment\Condition;

class Uptodate
    extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    protected $_inputType = 'numeric';

    /**
     * Initialize model
     *
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('\Magento\CustomerSegment\Model\Segment\Condition\Uptodate');
        $this->setValue(null);
    }

    /**
     * Customize default operator input by type mapper for some types
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['numeric'] = array('>=', '<=', '>', '<');
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Customize default operator options getter
     *
     * Inverted logic for UpToDate condition. For example, condition:
     * Period "equals or less" than 10 Days Up To Date - means:
     * days from _10 day before today_ till today: days >= (today - 10), etc.
     *
     * @return array
     */
    public function getDefaultOperatorOptions()
    {
        if (null === $this->_defaultOperatorOptions) {
            $this->_defaultOperatorOptions = array(
                '<='  => __('equals or greater than'),
                '>='  => __('equals or less than'),
                '<'   => __('greater than'),
                '>'   => __('less than')
            );
        }
        return $this->_defaultOperatorOptions;
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
            'label' => __('Up To Date'),
        );
    }

    /**
     * Get element input value type
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Period %1 %2 Days Up To Date', $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get condition subfilter type. Can be used in parent level queries
     *
     * @return string
     */
    public function getSubfilterType()
    {
        return 'date';
    }

    /**
     * Apply date subfilter to parent/base condition query
     *
     * @param string $fieldName base query field name
     * @param bool $requireValid strict validation flag
     * @param $website
     * @return string
     */
    public function getSubfilterSql($fieldName, $requireValid, $website)
    {
        $value = $this->getValue();
        if (!$value || !is_numeric($value)) {
            return false;
        }

        $limit = date('Y-m-d', strtotime("now -{$value} days"));
        //$operator = (($requireValid && $this->getOperator() == '==') ? '>' : '<');
        $operator = $this->getOperator();
        return sprintf("%s %s '%s'", $fieldName, $operator, $limit);
    }
}
