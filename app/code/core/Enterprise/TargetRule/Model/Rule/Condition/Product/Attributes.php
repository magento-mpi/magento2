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
    extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    /**
     * Attribute property that defines whether to use it for target rules
     *
     * @var string
     */
    protected $_isUsedForRuleProperty = 'is_used_for_promo_rules';

    /**
     * Target rule codes that do not allowed to select
     *
     * @var array
     */
    protected $_disabledTargetRuleCodes = array(
        'status' // products with status 'disabled' cannot be shown as related/cross-sells/up-sells thus rule code is useless
    );

    /**
     * Set condition type and value
     *
     */
    public function __construct()
    {
        parent::__construct();
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
            'label' => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Product Attributes')
        );
    }
}
