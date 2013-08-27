<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Enterprise_TargetRule_Model_Rule_Condition_Product_Attributes
    extends Magento_Rule_Model_Condition_Product_Abstract
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
     *
     *
     * @param Magento_Adminhtml_Helper_Data $adminhtmlData
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Data $adminhtmlData,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        parent::__construct($adminhtmlData, $context, $data);
        $this->setType('Enterprise_TargetRule_Model_Rule_Condition_Product_Attributes');
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
