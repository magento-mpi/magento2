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
 * Date range combo
 *
 * @method \Magento\CustomerSegment\Model\Segment\Condition\Daterange setType(string $type)
 * @method \Magento\CustomerSegment\Model\Segment\Condition\Daterange setValue(string $value)
 */
namespace Magento\CustomerSegment\Model\Segment\Condition;

class Daterange
    extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    /**
     * Input type for operator options
     *
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * Value form element
     *
     * @var \Magento\Data\Form\Element\Text
     */
    private $_valueElement = null;

    /**
     * Adminhtml data
     *
     * @var Magento_Adminhtml_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param Magento_Adminhtml_Helper_Data $adminhtmlData
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Data $adminhtmlData,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        parent::__construct($context, $data);

        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Daterange');
        $this->setValue(null);
    }

    /**
     * Inherited hierarchy options getter
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(
            'value' => $this->getType(),
            'label' => __('Date Range'),
        );
    }

    /**
     * Value element type getter
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Enable chooser selection button
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        return true;
    }

    /**
     * Avoid value distortion by possible options
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        return array();
    }

    /**
     * Chooser button HTML getter
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        return '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="'
            . $this->_viewUrl->getViewFileUrl('images/rule_chooser_trigger.gif')
            . '" alt="" class="v-middle rule-chooser-trigger"'
            . 'title="' . __('Open Chooser') . '" /></a>';
    }

    /**
     * Chooser URL getter
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        return $this->_adminhtmlData->getUrl('adminhtml/customersegment/chooserDaterange', array(
            'value_element_id' => $this->_valueElement->getId(),
        ));
    }

    /**
     * Render as HTML
     *
     * Chooser div is declared in such a way, that element value will be treated as is
     *
     * @return string
     */
    public function asHtml()
    {
        $this->_valueElement = $this->getValueElement();
        return $this->getTypeElementHtml()
            . __('Date Range %1 within %2', $this->getOperatorElementHtml(), $this->_valueElement->getHtml())
            . $this->getRemoveLinkHtml()
            . '<div class="rule-chooser no-split" url="' . $this->getValueElementChooserUrl() . '"></div>';
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
        $value = explode('...', $this->getValue());
        if (!isset($value[0]) || !isset($value[1])) {
            return false;
        }

        $regexp = '#^\d{4}-\d{2}-\d{2}$#';
        if (!preg_match($regexp, $value[0]) || !preg_match($regexp, $value[1])) {
            return false;
        }

        $start = $value[0];
        $end = $value[1];

        if (!$start || !$end) {
            return false;
        }

        $inOperator = (($requireValid && $this->getOperator() == '==') ? 'BETWEEN' : 'NOT BETWEEN');
        return sprintf("%s %s '%s' AND '%s'", $fieldName, $inOperator, $start, $end);
    }
}
