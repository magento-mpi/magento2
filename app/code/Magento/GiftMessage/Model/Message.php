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
 * Gift Message model
 *
 * @method Magento_GiftMessage_Model_Resource_Message _getResource()
 * @method Magento_GiftMessage_Model_Resource_Message getResource()
 * @method int getCustomerId()
 * @method Magento_GiftMessage_Model_Message setCustomerId(int $value)
 * @method string getSender()
 * @method Magento_GiftMessage_Model_Message setSender(string $value)
 * @method string getRecipient()
 * @method Magento_GiftMessage_Model_Message setRecipient(string $value)
 * @method string getMessage()
 * @method Magento_GiftMessage_Model_Message setMessage(string $value)
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftMessage_Model_Message extends Magento_Core_Model_Abstract
{
    /**
     * Allowed types of entities for using of gift messages
     *
     * @var array
     */
    static protected $_allowedEntityTypes = array(
        'order'         => 'Magento_Sales_Model_Order',
        'order_item'    => 'Magento_Sales_Model_Order_Item',
        'order_address' => 'Magento_Sales_Model_Order_Address',
        'quote'         => 'Magento_Sales_Model_Quote',
        'quote_item'    => 'Magento_Sales_Model_Quote_Item',
        'quote_address' => 'Magento_Sales_Model_Quote_Address',
        'quote_address_item' => 'Magento_Sales_Model_Quote_Address_Item'
    );

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento_GiftMessage_Model_Resource_Message');
    }

    /**
     * Return model from entity type
     *
     * @param string $type
     * @return Magento_Eav_Model_Entity_Abstract
     */
    public function getEntityModelByType($type)
    {
        $types = self::getAllowedEntityTypes();
        if(!isset($types[$type])) {
            throw new Magento_Core_Exception(__('Unknown entity type'));
        }

        return $this->_objectManager->get($types[$type]);
    }

    /**
     * Checks thats gift message is empty
     *
     * @return boolean
     */
    public function isMessageEmpty()
    {
        return trim($this->getMessage()) == '';
    }

    /**
     * Return list of allowed entities for using in gift messages
     *
     * @return array
     */
    static public function getAllowedEntityTypes()
    {
        return self::$_allowedEntityTypes;
    }

}
