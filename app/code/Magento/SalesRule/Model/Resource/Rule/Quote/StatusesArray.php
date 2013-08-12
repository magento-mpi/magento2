<?php
/**
 * User statuses option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_SalesRule_Model_Resource_Rule_Quote_StatusesArray implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * SalesRule Helper
     *
     * @var Magento_SalesRule_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_SalesRule_Helper_Data $salesRuleHelper
     */
    public function __construct(Magento_SalesRule_Helper_Data $salesRuleHelper)
    {
        $this->_helper = $salesRuleHelper;
    }

    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => $this->_helper->__('Active'),
            '0' => $this->_helper->__('Inactive'),
        );
    }
}
