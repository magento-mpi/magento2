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
namespace Magento\Sales\Model\Resource\Billing\Agreement;

class Collection
    extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
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
     * @var \Magento\Customer\Model\Resource\Customer
     */
    protected $_customerResource;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Customer\Model\Resource\Customer $customerResource
     * @param mixed $connection
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Customer\Model\Resource\Customer $customerResource,
        $connection = null,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_customerResource = $customerResource;
    }

    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Billing\Agreement', 'Magento\Sales\Model\Resource\Billing\Agreement');
    }

    /**
     * Add customer details(email, firstname, lastname) to select
     *
     * @return \Magento\Sales\Model\Resource\Billing\Agreement\Collection
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
