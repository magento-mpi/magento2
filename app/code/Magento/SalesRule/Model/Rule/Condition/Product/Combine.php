<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\SalesRule\Model\Rule\Condition\Product;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('\Magento\SalesRule\Model\Rule\Condition\Product\Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productCondition = \Mage::getModel('Magento\SalesRule\Model\Rule\Condition\Product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $pAttributes = array();
        $iAttributes = array();
        foreach ($productAttributes as $code=>$label) {
            if (strpos($code, 'quote_item_') === 0) {
                $iAttributes[] = array(
                    'value' => '\Magento\SalesRule\Model\Rule\Condition\Product|' . $code, 'label' => $label
                );
            } else {
                $pAttributes[] =
                    array('value' => '\Magento\SalesRule\Model\Rule\Condition\Product|' . $code, 'label' => $label);
            }
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => '\Magento\SalesRule\Model\Rule\Condition\Product\Combine',
                'label' => __('Conditions Combination')
            ),
            array('label' => __('Cart Item Attribute'),
                'value' => $iAttributes
            ),
            array('label' => __('Product Attribute'),
                'value' => $pAttributes
            ),
        ));
        return $conditions;
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
