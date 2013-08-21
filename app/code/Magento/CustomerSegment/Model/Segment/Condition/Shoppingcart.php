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
class Magento_CustomerSegment_Model_Segment_Condition_Shoppingcart
    extends Magento_CustomerSegment_Model_Condition_Abstract
{
    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Shoppingcart');
        $this->setValue(null);
    }

    /**
     * Get condition "selectors" for parent block
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $prefix = 'Magento_CustomerSegment_Model_Segment_Condition_Shoppingcart_';
        return array(
            'value' => array(
                Mage::getModel($prefix . 'Amount')->getNewChildSelectOptions(),
                Mage::getModel($prefix . 'Itemsquantity')->getNewChildSelectOptions(),
                Mage::getModel($prefix . 'Productsquantity')->getNewChildSelectOptions(),
            ),
            'label' => __('Shopping Cart'),
            'available_in_guest_mode' => true,
        );
    }
}
