<?php
/**
 * User statuses option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_SalesRule_Model_Resource_Rule_Quote_StatusesArray implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * SalesRule Helper
     *
     * @var Mage_SalesRule_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_SalesRule_Helper_Data $salesRuleHelper
     */
    public function __construct(Mage_SalesRule_Helper_Data $salesRuleHelper)
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
            '1' => __('Active'),
            '0' => __('Inactive'),
        );
    }
}
