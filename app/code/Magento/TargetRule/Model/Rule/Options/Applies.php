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
namespace Magento\TargetRule\Model\Rule\Options;

class Applies implements \Magento\Core\Model\Option\ArrayInterface
{

    /**
     * Target Rule model
     *
     * @var \Magento\TargetRule\Model\Rule
     */
    protected $_targetRuleModel;

    /**
     * @param \Magento\TargetRule\Model\Rule
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
