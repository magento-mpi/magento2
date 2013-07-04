<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User statuses option array
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @author     Magento Core Team <core@magentocommerce.com>
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
            '1' => $this->_helper->__('Active'),
            '0' => $this->_helper->__('Inactive'),
        );
    }
}
