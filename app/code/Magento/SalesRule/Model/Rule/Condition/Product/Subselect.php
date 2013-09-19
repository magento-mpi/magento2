<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_SalesRule_Model_Rule_Condition_Product_Subselect
    extends Magento_SalesRule_Model_Rule_Condition_Product_Combine
{
    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_SalesRule_Model_Rule_Condition_Product $ruleConditionProduct
     * @param array $data
     */
    public function __construct(
        Magento_Rule_Model_Condition_Context $context,
        Magento_SalesRule_Model_Rule_Condition_Product $ruleConditionProduct,
        array $data = array()
    ) {
        parent::__construct($context, $ruleConditionProduct, $data);
        $this->setType('Magento_SalesRule_Model_Rule_Condition_Product_Subselect')
            ->setValue(null);
    }

    /**
     * @param array $arr
     * @param string $key
     * @return $this
     */
    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);
        return $this;
    }

    /**
     * @param string $containerKey
     * @param string $itemKey
     * @return string
     */
    public function asXml($containerKey = 'conditions', $itemKey = 'condition')
    {
        $xml = '<attribute>' . $this->getAttribute() . '</attribute>'
            . '<operator>' . $this->getOperator() . '</operator>'
            . parent::asXml($containerKey, $itemKey);
        return $xml;
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'qty'  => __('total quantity'),
            'base_row_total'  => __('total amount'),
        ));
        return $this;
    }

    /**
     * @return $this
     */
    public function loadValueOptions()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '=='  => __('is'),
            '!='  => __('is not'),
            '>='  => __('equals or greater than'),
            '<='  => __('equals or less than'),
            '>'   => __('greater than'),
            '<'   => __('less than'),
            '()'  => __('is one of'),
            '!()' => __('is not one of'),
        ));
        return $this;
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . __(
            "If %1 %2 %3 for a subselection of items in cart matching %4 of these conditions:",
            $this->getAttributeElement()->getHtml(),
            $this->getOperatorElement()->getHtml(),
            $this->getValueElement()->getHtml(),
            $this->getAggregatorElement()->getHtml()
        );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * validate
     *
     * @param Magento_Object $object Quote
     * @return boolean
     */
    public function validate(Magento_Object $object)
    {
        if (!$this->getConditions()) {
            return false;
        }
        $attr = $this->getAttribute();
        $total = 0;
        foreach ($object->getQuote()->getAllVisibleItems() as $item) {
            if (parent::validate($item)) {
                $total += $item->getData($attr);
            }
        }
        return $this->validateAttribute($total);
    }
}
