<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model\Resource\Visitor\Online;

use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;

/**
 * Log Online visitors collection
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Joined fields array
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * @var \Magento\Eav\Helper\Data
     */
    protected $_eavHelper;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Helper\Data $eavHelper
     * @param mixed $connection
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Eav\Helper\Data $eavHelper,
        $connection = null,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_eavHelper = $eavHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Log\Model\Visitor\Online', 'Magento\Log\Model\Resource\Visitor\Online');
    }

    /**
     * Add Customer data to collection
     *
     * @return $this
     */
    public function addCustomerData()
    {
        // alias => attribute_code
        $attributes = array(
            'customer_lastname' => 'lastname',
            'customer_firstname' => 'firstname',
            'customer_email' => 'email'
        );

        foreach ($attributes as $alias => $attributeCode) {

            $attribute = $this->_eavHelper->getAttributeMetadata(
                CustomerMetadataServiceInterface::ENTITY_TYPE_CUSTOMER,
                $attributeCode
            );

            $tableAlias = 'customer_' . $attributeCode;

            if ($attribute['backend_type'] == 'static') {
                $this->getSelect()->joinLeft(
                    array($tableAlias => $attribute['attribute_table']),
                    sprintf('%s.entity_id=main_table.customer_id', $tableAlias),
                    array($alias => $attributeCode)
                );
                $this->_fields[$alias] = sprintf('%s.%s', $tableAlias, $attributeCode);
            } else {
                $joinConds  = array(
                    sprintf('%s.entity_id=main_table.customer_id', $tableAlias),
                    $this->getConnection()->quoteInto($tableAlias . '.attribute_id=?', $attribute['attribute_id'])
                );
                $this->getSelect()->joinLeft(
                    array($tableAlias => $attribute['attribute_table']),
                    join(' AND ', $joinConds),
                    array($alias => 'value')
                );
                $this->_fields[$alias] = sprintf('%s.value', $tableAlias);
            }
        }
        $this->setFlag('has_customer_data', true);
        return $this;
    }

    /**
     * Filter collection by specified website(s)
     *
     * @param int|int[] $websiteIds
     * @return $this
     */
    public function addWebsiteFilter($websiteIds)
    {
        if ($this->getFlag('has_customer_data')) {
            $this->getSelect()->where('customer_email.website_id IN (?)', $websiteIds);
        }
        return $this;
    }

    /**
     * Add field filter to collection
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @param string $field
     * @param null|string|array $condition
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     *
     * @see self::_getConditionSql for $condition
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (isset($this->_fields[$field])) {
            $field = $this->_fields[$field];
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
