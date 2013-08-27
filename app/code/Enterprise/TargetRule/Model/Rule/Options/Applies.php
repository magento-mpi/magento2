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
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_TargetRule_Model_Rule_Options_Applies implements Magento_Core_Model_Option_ArrayInterface
{

    /**
     * Target Rule model
     *
     * @var Enterprise_TargetRule_Model_Rule
     */
    protected $_targetRuleModel;

    /**
     * @param Enterprise_TargetRule_Model_Rule
     */
    public function __construct(Enterprise_TargetRule_Model_Rule $targetRuleModel)
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
