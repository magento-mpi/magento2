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
 * Sales conditions combine
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Combine
    extends Enterprise_CustomerSegment_Model_Condition_Combine_Abstract
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array_merge_recursive(parent::getNewChildSelectOptions(), array(
            Mage::getModel('Enterprise_CustomerSegment_Model_Segment_Condition_Order_Status')->getNewChildSelectOptions(),
            // date ranges
            array(
                'value' => array(
                    Mage::getModel('Enterprise_CustomerSegment_Model_Segment_Condition_Uptodate')->getNewChildSelectOptions(),
                    Mage::getModel('Enterprise_CustomerSegment_Model_Segment_Condition_Daterange')->getNewChildSelectOptions(),
                ),
                'label' => __('Date Ranges')
            ),
        ));
    }

    /**
     * Init attribute select options
     *
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Combine
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'total'   => __('Total'),
            'average' => __('Average'),
        ));
        return $this;
    }

    /**
     * Get input type for attribute value.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Check if validation should be strict
     *
     * @return bool
     */
    protected function _getRequiredValidation()
    {
        return true;
    }

    /**
     * Get field names map for subfilters
     *
     * @return array
     */
    protected function _getSubfilterMap()
    {
        return array(
            'order' => 'sales_order.status',
            'date' => 'sales_order.created_at',
        );
    }
}
