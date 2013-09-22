<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Model;

/**
 * Factory class for Eav Entity Types
 */
class TypeFactory
{
    /**
     * Allowed types of entities for using of gift messages
     *
     * @var array
     */
    protected $_allowedEntityTypes = array(
        'order'         => 'Magento\Sales\Model\Order',
        'order_item'    => 'Magento\Sales\Model\Order\Item',
        'order_address' => 'Magento\Sales\Model\Order_Address',
        'quote'         => 'Magento\Sales\Model\Quote',
        'quote_item'    => 'Magento\Sales\Model\Quote\Item',
        'quote_address' => 'Magento\Sales\Model\Quote\Address',
        'quote_address_item' => 'Magento\Sales\Model\Quote\Address\Item'
    );

    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create type object
     *
     * @param $eavType
     *
     * @return Magento_Eav_Model_Entity_Abstract
     * @throws \Magento\Core\Exception
     */
    public function createType($eavType)
    {
        $types = $this->_allowedEntityTypes;
        if(!isset($types[$eavType])) {
            throw new \Magento\Core\Exception(__('Unknown entity type'));
        }
        return $this->_objectManager->create($types[$eavType]);
    }
}
