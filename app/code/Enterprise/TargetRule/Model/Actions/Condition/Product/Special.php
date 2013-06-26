<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Action Special Product Attributes Condition Model
 *
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 */
class Enterprise_TargetRule_Model_Actions_Condition_Product_Special
    extends Mage_Rule_Model_Condition_Product_Abstract
{
    /**
     * Set condition type and value
     */
    public function __construct(Mage_Rule_Model_Condition_Context $context)
    {
        parent::__construct($context);
        $this->setType('Enterprise_TargetRule_Model_Actions_Condition_Product_Special');
        $this->setValue(null);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array(
                'value' => 'Enterprise_TargetRule_Model_Actions_Condition_Product_Special_Price',
                'label' => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Price (percentage)')
            )
        );

        return array(
            'value' => $conditions,
            'label' => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Product Special')
        );
    }
}
