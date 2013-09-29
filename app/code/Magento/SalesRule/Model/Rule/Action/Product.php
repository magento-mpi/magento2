<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\SalesRule\Model\Rule\Action;

class Product extends \Magento\Rule\Model\Action\AbstractAction
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
