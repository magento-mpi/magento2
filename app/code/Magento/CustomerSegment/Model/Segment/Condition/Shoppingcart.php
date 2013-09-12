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
 * Shopping cart conditions options group
 */
namespace Magento\CustomerSegment\Model\Segment\Condition;

class Shoppingcart
    extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('\Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart');
        $this->setValue(null);
    }

    /**
     * Get condition "selectors" for parent block
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $prefix = 'Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart\\';
        return array(
            'value' => array(
                \Mage::getModel($prefix . 'Amount')->getNewChildSelectOptions(),
                \Mage::getModel($prefix . 'Itemsquantity')->getNewChildSelectOptions(),
                \Mage::getModel($prefix . 'Productsquantity')->getNewChildSelectOptions(),
            ),
            'label' => __('Shopping Cart'),
            'available_in_guest_mode' => true,
        );
    }
}
