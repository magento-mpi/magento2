<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer conditions options group
 */
namespace Magento\CustomerSegment\Model\Segment\Condition;

class Customer
    extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Customer');
        $this->setValue(null);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $prefix = 'Magento\CustomerSegment\Model\Segment\Condition\Customer\\';
        $conditions = \Mage::getModel($prefix . 'Attributes')->getNewChildSelectOptions();
        $conditions = array_merge($conditions, \Mage::getModel($prefix . 'Newsletter')->getNewChildSelectOptions());
        $conditions = array_merge($conditions, \Mage::getModel($prefix . 'Storecredit')->getNewChildSelectOptions());
        return array(
            'value' => $conditions,
            'label' => __('Customer')
        );
    }
}
