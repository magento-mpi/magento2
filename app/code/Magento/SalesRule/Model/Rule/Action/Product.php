<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_SalesRule_Model_Rule_Action_Product extends Magento_Rule_Model_Action_Abstract
{
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'rule_price'=>__('Special Price'),
        ));
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            'to_fixed'=>__('To Fixed Value'),
            'to_percent'=>__('To Percentage'),
            'by_fixed'=>__('By Fixed value'),
            'by_percent'=>__('By Percentage'),
        ));
        return $this;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().__("Update product's %1 %2: %3", $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml());
        $html.= $this->getRemoveLinkHtml();
        return $html;
    }
}
