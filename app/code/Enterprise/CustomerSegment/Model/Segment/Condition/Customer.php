<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer conditions options group
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Customer
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    /**
     * Class constructor
     */
    public function __construct(Mage_Rule_Model_Condition_Context $context)
    {
        parent::__construct($context);
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Customer');
        $this->setValue(null);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array();
        $prefix = 'Enterprise_CustomerSegment_Model_Segment_Condition_Customer_';
        $conditions = Mage::getModel($prefix.'Attributes')->getNewChildSelectOptions();
        $conditions = array_merge($conditions, Mage::getModel($prefix.'Newsletter')->getNewChildSelectOptions());
        $conditions = array_merge($conditions, Mage::getModel($prefix.'Storecredit')->getNewChildSelectOptions());
        return array(
            'value' => $conditions,
            'label'=>Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Customer')
        );
    }
}
