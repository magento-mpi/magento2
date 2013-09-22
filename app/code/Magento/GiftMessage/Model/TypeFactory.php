<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory class for Eav Entity Types
 */
class Magento_GiftMessage_Model_TypeFactory
{
    /**
     * Allowed types of entities for using of gift messages
     *
     * @var array
     */
    protected $_allowedEntityTypes = array(
        'order'         => 'Magento_Sales_Model_Order',
        'order_item'    => 'Magento_Sales_Model_Order_Item',
        'order_address' => 'Magento_Sales_Model_Order_Address',
        'quote'         => 'Magento_Sales_Model_Quote',
        'quote_item'    => 'Magento_Sales_Model_Quote_Item',
        'quote_address' => 'Magento_Sales_Model_Quote_Address',
        'quote_address_item' => 'Magento_Sales_Model_Quote_Address_Item'
    );

    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create type object
     *
     * @param $eavType
     *
     * @return Magento_Eav_Model_Entity_Abstract
     * @throws Magento_Core_Exception
     */
    public function createType($eavType)
    {
        $types = $this->_allowedEntityTypes;
        if(!isset($types[$eavType])) {
            throw new Magento_Core_Exception(__('Unknown entity type'));
        }
        return $this->_objectManager->create($types[$eavType]);
    }
}
