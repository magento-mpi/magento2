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
 * Orders conditions options group
 */
namespace Magento\CustomerSegment\Model\Segment\Condition;

class Sales
    extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('\Magento\CustomerSegment\Model\Segment\Condition\Sales');
        $this->setValue(null);
    }

    /**
     * Get condition "selectors"
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(
            'value' => array(
                array( // order address combo
                    'value' => '\Magento\CustomerSegment\Model\Segment\Condition\Order\Address',
                    'label' => __('Order Address')),
                array(
                    'value' => '\Magento\CustomerSegment\Model\Segment\Condition\Sales\Salesamount',
                    'label' => __('Sales Amount')),
                array(
                    'value' => '\Magento\CustomerSegment\Model\Segment\Condition\Sales\Ordersnumber',
                    'label' => __('Number of Orders')),
                array(
                    'value' => '\Magento\CustomerSegment\Model\Segment\Condition\Sales\Purchasedquantity',
                    'label' => __('Purchased Quantity')),
             ),
            'label' => __('Sales')
        );
    }
}
