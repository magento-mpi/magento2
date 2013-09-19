<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Log Online visitors collection
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Log_Model_Resource_Visitor_Online_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * joined fields array
     *
     * @var array
     */
    protected $_fields   = array();

    /**
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_customerFactory = $customerFactory;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }


    /**
     * Initialize collection model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Log_Model_Visitor_Online', 'Magento_Log_Model_Resource_Visitor_Online');
    }

    /**
     * Add Customer data to collection
     *
     * @return Magento_Log_Model_Resource_Visitor_Online_Collection
     */
    public function addCustomerData()
    {
        $customer   = $this->_customerFactory->create();
        // alias => attribute_code
        $attributes = array(
            'customer_lastname'     => 'lastname',
            'customer_firstname'    => 'firstname',
            'customer_email'        => 'email'
        );

        foreach ($attributes as $alias => $attributeCode) {
            $attribute = $customer->getAttribute($attributeCode);
            /* @var $attribute Magento_Eav_Model_Entity_Attribute_Abstract */

            if ($attribute->getBackendType() == 'static') {
                $tableAlias = 'customer_' . $attribute->getAttributeCode();

                $this->getSelect()->joinLeft(
                    array($tableAlias => $attribute->getBackend()->getTable()),
                    sprintf('%s.entity_id=main_table.customer_id', $tableAlias),
                    array($alias => $attribute->getAttributeCode())
                );

                $this->_fields[$alias] = sprintf('%s.%s', $tableAlias, $attribute->getAttributeCode());
            }
            else {
                $tableAlias = 'customer_' . $attribute->getAttributeCode();

                $joinConds  = array(
                    sprintf('%s.entity_id=main_table.customer_id', $tableAlias),
                    $this->getConnection()->quoteInto($tableAlias . '.attribute_id=?', $attribute->getAttributeId())
                );

                $this->getSelect()->joinLeft(
                    array($tableAlias => $attribute->getBackend()->getTable()),
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
     * @param int|array $websiteIds
     * @return Magento_Log_Model_Resource_Visitor_Online_Collection
     */
    public function addWebsiteFilter($websiteIds)
    {
        if ($this->getFlag('has_customer_data')) {
            $this->getSelect()
                ->where('customer_email.website_id IN (?)', $websiteIds);
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
     * @see self::_getConditionSql for $condition
     *
     * @param string $field
     * @param null|string|array $condition
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (isset($this->_fields[$field])) {
            $field = $this->_fields[$field];
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
