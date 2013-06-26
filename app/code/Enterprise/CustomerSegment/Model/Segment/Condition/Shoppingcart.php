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
 * Shopping cart conditions options group
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Shoppingcart
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    /**
     * Class constructor
     */
    public function __construct(Mage_Rule_Model_Condition_Context $context)
    {
        parent::__construct($context);
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Shoppingcart');
        $this->setValue(null);
    }

    /**
     * Get condition "selectors" for parent block
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $prefix = 'Enterprise_CustomerSegment_Model_Segment_Condition_Shoppingcart_';
        return array('value' => array(
                Mage::getModel($prefix.'Amount')->getNewChildSelectOptions(),
                Mage::getModel($prefix.'Itemsquantity')->getNewChildSelectOptions(),
                Mage::getModel($prefix.'Productsquantity')->getNewChildSelectOptions(),
            ),
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Shopping Cart'),
            'available_in_guest_mode' => true,
        );
    }
}
