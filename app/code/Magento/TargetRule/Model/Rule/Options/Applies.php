<?php
/**
 * {license_notice}
 *
 * @category    Enterprice
 * @package     Enterprice_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Statuses option array
 *
 * @category   Magento
 * @package    Magento_TargetRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_TargetRule_Model_Rule_Options_Applies implements Magento_Core_Model_Option_ArrayInterface
{

    /**
     * Target Rule model
     *
     * @var Magento_TargetRule_Model_Rule
     */
    protected $_targetRuleModel;

    /**
     * @param Magento_TargetRule_Model_Rule
     */
    public function __construct(Magento_TargetRule_Model_Rule $targetRuleModel)
    {
        $this->_targetRuleModel = $targetRuleModel;
    }

    /**
     * Return statuses array
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_targetRuleModel->getAppliesToOptions();
    }
}
