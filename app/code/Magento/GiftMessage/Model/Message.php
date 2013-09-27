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
     * @var Magento_GiftMessage_Model_TypeFactory
     */
    protected $_typeFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_GiftMessage_Model_Resource_Message $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param Magento_GiftMessage_Model_TypeFactory $typeFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_GiftMessage_Model_Resource_Message $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        Magento_GiftMessage_Model_TypeFactory $typeFactory,
        array $data = array()
    ) {
        $this->_typeFactory = $typeFactory;
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
     *
     * @return Magento_Eav_Model_Entity_Abstract
     */
    public function getEntityModelByType($type)
    {
        return $this->_typeFactory->createType($type);
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
}
