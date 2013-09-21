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
 * Billing agreements resource collection
 */
class Magento_Sales_Model_Resource_Billing_Agreement_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Mapping for fields
     *
     * @var array
     */
    protected $_map = array('fields' => array(
        'customer_email'       => 'ce.email',
        'customer_firstname'   => 'firstname.value',
        'customer_lastname'    => 'lastname.value',
        'agreement_created_at' => 'main_table.created_at',
        'agreement_updated_at' => 'main_table.updated_at',
    ));

    /**
     * @var Magento_Customer_Model_Resource_Customer
     */
    protected $_customerResource;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Customer_Model_Resource_Customer $customerResource
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Customer_Model_Resource_Customer $customerResource,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
        $this->_customerResource = $customerResource;
    }
    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Billing_Agreement', 'Magento_Sales_Model_Resource_Billing_Agreement');
    }

    /**
     * Add customer details(email, firstname, lastname) to select
     *
     * @return Magento_Sales_Model_Resource_Billing_Agreement_Collection
     */
    public function addCustomerDetails()
    {
        $select = $this->getSelect()->joinInner(
            array('ce' => $this->getTable('customer_entity')),
            'ce.entity_id = main_table.customer_id',
            array('customer_email' => 'email')
        );

        $adapter  = $this->getConnection();
        $attr     = $this->_customerResource->getAttribute('firstname');
        $joinExpr = 'firstname.entity_id = main_table.customer_id AND '
            . $adapter->quoteInto('firstname.entity_type_id = ?', $this->_customerResource->getTypeId()) . ' AND '
            . $adapter->quoteInto('firstname.attribute_id = ?', $attr->getAttributeId());

        $select->joinLeft(
            array('firstname' => $attr->getBackend()->getTable()),
            $joinExpr,
            array('customer_firstname' => 'value')
        );

        $attr = $this->_customerResource->getAttribute('lastname');
        $joinExpr = 'lastname.entity_id = main_table.customer_id AND '
            . $adapter->quoteInto('lastname.entity_type_id = ?', $this->_customerResource->getTypeId()) . ' AND '
            . $adapter->quoteInto('lastname.attribute_id = ?', $attr->getAttributeId());

        $select->joinLeft(
            array('lastname' => $attr->getBackend()->getTable()),
            $joinExpr,
            array('customer_lastname' => 'value')
        );
        return $this;
    }
}
