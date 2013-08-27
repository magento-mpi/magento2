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
 * Order address attribute conditions combine
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address_Combine
    extends Enterprise_CustomerSegment_Model_Condition_Combine_Abstract
{
    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address_Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $prefix = 'Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address_';
        $result = array_merge_recursive(parent::getNewChildSelectOptions(), array(
            array(
                'value' => $this->getType(),
                'label' => __('Conditions Combination')),
            Mage::getModel($prefix.'Type')->getNewChildSelectOptions(),
            Mage::getModel($prefix.'Attributes')->getNewChildSelectOptions(),
        ));
        return $result;
    }
}
