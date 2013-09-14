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
 * @method \Magento\GiftMessage\Model\Resource\Message _getResource()
 * @method \Magento\GiftMessage\Model\Resource\Message getResource()
 * @method int getCustomerId()
 * @method \Magento\GiftMessage\Model\Message setCustomerId(int $value)
 * @method string getSender()
 * @method \Magento\GiftMessage\Model\Message setSender(string $value)
 * @method string getRecipient()
 * @method \Magento\GiftMessage\Model\Message setRecipient(string $value)
 * @method string getMessage()
 * @method \Magento\GiftMessage\Model\Message setMessage(string $value)
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftMessage\Model;

class Message extends \Magento\Core\Model\AbstractModel
{
    /**
     * Allowed types of entities for using of gift messages
     *
     * @var array
     */
    static protected $_allowedEntityTypes = array(
        'order'         => 'Magento\Sales\Model\Order',
        'order_item'    => 'Magento\Sales\Model\Order\Item',
        'order_address' => 'Magento\Sales\Model\Order\Address',
        'quote'         => 'Magento\Sales\Model\Quote',
        'quote_item'    => 'Magento\Sales\Model\Quote\Item',
        'quote_address' => 'Magento\Sales\Model\Quote\Address',
        'quote_address_item' => 'Magento\Sales\Model\Quote\Address\Item'
    );

    protected function _construct()
    {
        $this->_init('Magento\GiftMessage\Model\Resource\Message');
    }

    /**
     * Return model from entity type
     *
     * @param string $type
     * @return \Magento\Eav\Model\Entity\AbstractEntity
     */
    public function getEntityModelByType($type)
    {
        $types = self::getAllowedEntityTypes();
        if(!isset($types[$type])) {
            \Mage::throwException(__('Unknown entity type'));
        }

        return \Mage::getModel($types[$type]);
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
