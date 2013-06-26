<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_SalesRule_Model_Rule_Condition_Product_Subselect
    extends Mage_SalesRule_Model_Rule_Condition_Product_Combine
{
    public function __construct(Mage_Rule_Model_Condition_Context $context)
    {
        parent::__construct($context);
        $this->setType('Mage_SalesRule_Model_Rule_Condition_Product_Subselect')
            ->setValue(null);
    }

    public function loadArray($arr, $key='conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);
        return $this;
    }

    public function asXml($containerKey='conditions', $itemKey='condition')
    {
        $xml = '<attribute>'.$this->getAttribute().'</attribute>'
            . '<operator>'.$this->getOperator().'</operator>'
            . parent::asXml($containerKey, $itemKey);
        return $xml;
    }

    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'qty'  => Mage::helper('Mage_SalesRule_Helper_Data')->__('total quantity'),
            'base_row_total'  => Mage::helper('Mage_SalesRule_Helper_Data')->__('total amount'),
        ));
        return $this;
    }

    public function loadValueOptions()
    {
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '=='  => Mage::helper('Mage_Rule_Helper_Data')->__('is'),
            '!='  => Mage::helper('Mage_Rule_Helper_Data')->__('is not'),
            '>='  => Mage::helper('Mage_Rule_Helper_Data')->__('equals or greater than'),
            '<='  => Mage::helper('Mage_Rule_Helper_Data')->__('equals or less than'),
            '>'   => Mage::helper('Mage_Rule_Helper_Data')->__('greater than'),
            '<'   => Mage::helper('Mage_Rule_Helper_Data')->__('less than'),
            '()'  => Mage::helper('Mage_Rule_Helper_Data')->__('is one of'),
            '!()' => Mage::helper('Mage_Rule_Helper_Data')->__('is not one of'),
        ));
        return $this;
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().
        Mage::helper('Mage_SalesRule_Helper_Data')->__("If %s %s %s for a subselection of items in cart matching %s of these conditions:", $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * validate
     *
     * @param Varien_Object $object Quote
     * @return boolean
     */
    public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return false;
        }

//        $value = $this->getValue();
//        $aggregatorArr = explode('/', $this->getAggregator());
//        $this->setValue((int)$aggregatorArr[0])->setAggregator($aggregatorArr[1]);

        $attr = $this->getAttribute();
        $total = 0;
        foreach ($object->getQuote()->getAllVisibleItems() as $item) {
            if (parent::validate($item)) {
                $total += $item->getData($attr);
            }
        }
//        $this->setAggregator(join('/', $aggregatorArr))->setValue($value);

        return $this->validateAttribute($total);
    }
}
