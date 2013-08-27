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
    extends Magento_Rule_Model_Condition_Product_Abstract
{
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
                'label' => __('Price (percentage)')
            )
        );

        return array(
            'value' => $conditions,
            'label' => __('Product Special')
        );
    }
}
