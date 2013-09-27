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
     * @var Magento_CustomerSegment_Model_ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @param Magento_CustomerSegment_Model_ConditionFactory $conditionFactory
     * @param Magento_CustomerSegment_Model_Resource_Segment $resourceSegment
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerSegment_Model_ConditionFactory $conditionFactory,
        Magento_CustomerSegment_Model_Resource_Segment $resourceSegment,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_conditionFactory = $conditionFactory;
        parent::__construct($resourceSegment, $context, $data);
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
        return array(
            'value' => array(
                $this->_conditionFactory->create('Shoppingcart_Amount')->getNewChildSelectOptions(),
                $this->_conditionFactory->create('Shoppingcart_Itemsquantity')->getNewChildSelectOptions(),
                $this->_conditionFactory->create('Shoppingcart_Productsquantity')->getNewChildSelectOptions(),
            ),
            'label' => __('Shopping Cart'),
            'available_in_guest_mode' => true,
        );
    }
}
