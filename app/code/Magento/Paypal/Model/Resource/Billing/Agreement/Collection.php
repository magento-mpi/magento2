<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Resource\Billing\Agreement;

use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;

/**
 * Billing agreements resource collection
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Mapping for fields
     *
     * @var array
     */
    protected $_map = array(
        'fields' => array(
            'customer_email' => 'ce.email',
            'customer_firstname' => 'firstname.value',
            'customer_lastname' => 'lastname.value',
            'agreement_created_at' => 'main_table.created_at',
            'agreement_updated_at' => 'main_table.updated_at'
        )
    );

    /**
     * @var \Magento\Customer\Model\Resource\Customer
     */
    protected $_customerResource;

    /**
     * @var \Magento\Eav\Helper\Data
     */
    protected $_eavHelper;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Customer\Model\Resource\Customer $customerResource
     * @param \Magento\Eav\Helper\Data $eavHelper
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Customer\Model\Resource\Customer $customerResource,
        \Magento\Eav\Helper\Data $eavHelper,
        $connection = null,
        \Magento\Framework\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_eavHelper = $eavHelper;
        $this->_customerResource = $customerResource;
    }

    /**
     * Collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Paypal\Model\Billing\Agreement', 'Magento\Paypal\Model\Resource\Billing\Agreement');
    }

    /**
     * Add customer details(email, firstname, lastname) to select
     *
     * @return $this
     */
    public function addCustomerDetails()
    {
        $select = $this->getSelect()->joinInner(
            array('ce' => $this->getTable('customer_entity')),
            'ce.entity_id = main_table.customer_id',
            array('customer_email' => 'email')
        );

        $adapter = $this->getConnection();
        $firstNameMetadata = $this->_eavHelper->getAttributeMetadata(
            CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
            'firstname'
        );
        $joinExpr = 'firstname.entity_id = main_table.customer_id AND ' . $adapter->quoteInto(
            'firstname.entity_type_id = ?',
            $firstNameMetadata['entity_type_id']
        ) . ' AND ' . $adapter->quoteInto(
            'firstname.attribute_id = ?',
            $firstNameMetadata['attribute_id']
        );

        $select->joinLeft(
            array('firstname' => $firstNameMetadata['attribute_table']),
            $joinExpr,
            array('customer_firstname' => 'value')
        );

        $lastNameMetadata = $this->_eavHelper->getAttributeMetadata(
            CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
            'lastname'
        );
        $joinExpr = 'lastname.entity_id = main_table.customer_id AND ' . $adapter->quoteInto(
            'lastname.entity_type_id = ?',
            $lastNameMetadata['entity_type_id']
        ) . ' AND ' . $adapter->quoteInto(
            'lastname.attribute_id = ?',
            $lastNameMetadata['attribute_id']
        );

        $select->joinLeft(
            array('lastname' => $lastNameMetadata['attribute_table']),
            $joinExpr,
            array('customer_lastname' => 'value')
        );
        return $this;
    }
}
