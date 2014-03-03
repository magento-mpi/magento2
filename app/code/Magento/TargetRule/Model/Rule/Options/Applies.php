<?php
/**
 * {license_notice}
 *
 * @category    Enterprice
 * @package     Enterprice_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Rule\Options;

/**
 * Statuses option array
 *
 * @category   Magento
 * @package    Magento_TargetRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Applies implements \Magento\Option\ArrayInterface
{
    /**
     * Target Rule model
     *
     * @var \Magento\TargetRule\Model\Rule
     */
    protected $_targetRuleModel;

    /**
     * @param \Magento\TargetRule\Model\Rule $targetRuleModel
     */
    public function __construct(\Magento\TargetRule\Model\Rule $targetRuleModel)
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
