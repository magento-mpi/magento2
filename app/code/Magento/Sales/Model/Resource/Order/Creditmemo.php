<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat sales order creditmemo resource
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Creditmemo extends Magento_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix                  = 'sales_order_creditmemo_resource';

    /**
     * Is grid available
     *
     * @var bool
     */
    protected $_grid                         = true;

    /**
     * Flag for using of increment id
     *
     * @var bool
     */
    protected $_useIncrementId               = true;

    /**
     * Entity code for increment id (Eav entity code)
     *
     * @var string
     */
    protected $_entityTypeForIncrementId     = 'creditmemo';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_flat_creditmemo', 'entity_id');
    }

    /**
     * Init virtual grid records for entity
     *
     * @return Magento_Sales_Model_Resource_Order_Creditmemo
     */
    protected function _initVirtualGridColumns()
    {
        parent::_initVirtualGridColumns();
        $adapter          = $this->getReadConnection();
        $checkedFirstname = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
        $checkedLastname  = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));
        $concatName       = $adapter->getConcatSql(array($checkedFirstname, $adapter->quote(' '), $checkedLastname));

        $this->addVirtualGridColumn(
            'billing_name',
            'sales_flat_order_address',
            array('billing_address_id' => 'entity_id'),
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
