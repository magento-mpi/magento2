<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat sales order shipment resource
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Shipment extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix                  = 'sales_order_shipment_resource';

    /**
     * Is grid available
     *
     * @var bool
     */
    protected $_grid                         = true;

    /**
     * Use increment id
     *
     * @var bool
     */
    protected $_useIncrementId               = true;

    /**
     * Entity type for increment id
     *
     * @var string
     */
    protected $_entityTypeForIncrementId     = 'shipment';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_flat_shipment', 'entity_id');
    }

    /**
     * Init virtual grid records for entity
     *
     * @return Mage_Sales_Model_Resource_Order_Shipment
     */
    protected function _initVirtualGridColumns()
    {
        parent::_initVirtualGridColumns();
        $adapter          = $this->getReadConnection();
        $checkedFirstname = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
        $checkedLastname  = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));
        $concatName       = $adapter->getConcatSql(array($checkedFirstname, $adapter->quote(' '), $checkedLastname));

        $this->addVirtualGridColumn(
            'shipping_name',
            'sales_flat_order_address',
            array('shipping_address_id' => 'entity_id'),
            $concatName
        )
        ->addVirtualGridColumn(
            'order_increment_id',
            'sales_flat_order',
            array('order_id' => 'entity_id'),
            'increment_id'
        )
        ->addVirtualGridColumn(
            'order_created_at',
            'sales_flat_order',
            array('order_id' => 'entity_id'),
            'created_at'
        );

        return $this;
    }
}
