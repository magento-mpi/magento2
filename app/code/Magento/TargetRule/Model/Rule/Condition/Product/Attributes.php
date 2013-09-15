<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\TargetRule\Model\Rule\Condition\Product;

class Attributes
    extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * Attribute property that defines whether to use it for target rules
     *
     * @var string
     */
    protected $_isUsedForRuleProperty = 'is_used_for_promo_rules';

    /**
     * Target rule codes that do not allowed to select
     * Products with status 'disabled' cannot be shown as related/cross-sells/up-sells thus rule code is useless
     *
     * @var array
     */
    protected $_disabledTargetRuleCodes = array('status');

    /**
     * Set condition type and value
     *
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $backendData,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        parent::__construct($backendData, $context, $data);
        $this->setType('Magento\TargetRule\Model\Rule\Condition\Product\Attributes');
        $this->setValue(null);
    }

    /**
     * Prepare child rules option list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = array();
        foreach ($attributes as $code => $label) {
            if (! in_array($code, $this->_disabledTargetRuleCodes)) {
                $conditions[] = array(
                    'value' => $this->getType() . '|' . $code,
                    'label' => $label
                );
            }
        }

        return array(
            'value' => $conditions,
            'label' => __('Product Attributes')
        );
    }
}
