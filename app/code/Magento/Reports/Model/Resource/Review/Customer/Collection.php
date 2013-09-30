<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Report Customers Review collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Review_Customer_Collection extends Magento_Review_Model_Resource_Review_Collection
{
    /**
     * @var Magento_Customer_Model_Resource_Customer
     */
    protected $_customerResource;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Review_Helper_Data $reviewData
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Rating_Model_Rating_Option_VoteFactory $voteFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Customer_Model_Resource_Customer $customerResource
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Review_Helper_Data $reviewData,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Rating_Model_Rating_Option_VoteFactory $voteFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Customer_Model_Resource_Customer $customerResource,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_customerResource = $customerResource;
        parent::__construct(
            $eventManager, $logger, $reviewData, $fetchStrategy,
            $entityFactory, $voteFactory, $storeManager, $resource
        );
    }

    /**
     * Init Select
     *
     * @return Magento_Reports_Model_Resource_Review_Customer_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinCustomers();
        return $this;
    }

    /**
     * Join customers
     *
     * @return Magento_Reports_Model_Resource_Review_Customer_Collection
     */
    protected function _joinCustomers()
    {
        /** @var $adapter Magento_DB_Adapter_Interface */
        $adapter            = $this->getConnection();
        /** @var $firstnameAttr Magento_Eav_Model_Entity_Attribute */
        $firstnameAttr      = $this->_customerResource->getAttribute('firstname');
        /** @var $lastnameAttr Magento_Eav_Model_Entity_Attribute */
        $lastnameAttr       = $this->_customerResource->getAttribute('lastname');

        $firstnameCondition = array('table_customer_firstname.entity_id = detail.customer_id');

        if ($firstnameAttr->getBackend()->isStatic()) {
            $firstnameField = 'firstname';
        } else {
            $firstnameField = 'value';
            $firstnameCondition[] = $adapter->quoteInto('table_customer_firstname.attribute_id = ?',
                (int)$firstnameAttr->getAttributeId());
        }

        $this->getSelect()->joinInner(
            array('table_customer_firstname' => $firstnameAttr->getBackend()->getTable()),
            implode(' AND ', $firstnameCondition),
            array());


        $lastnameCondition  = array('table_customer_lastname.entity_id = detail.customer_id');
        if ($lastnameAttr->getBackend()->isStatic()) {
            $lastnameField = 'lastname';
        } else {
            $lastnameField = 'value';
            $lastnameCondition[] = $adapter->quoteInto('table_customer_lastname.attribute_id = ?',
                (int)$lastnameAttr->getAttributeId());
        }

        //Prepare fullname field result
        $customerFullname = $adapter->getConcatSql(array(
            "table_customer_firstname.{$firstnameField}",
            "table_customer_lastname.{$lastnameField}"
        ), ' ');
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS)
            ->joinInner(
                array('table_customer_lastname' => $lastnameAttr->getBackend()->getTable()),
                implode(' AND ', $lastnameCondition),
                array())
            ->columns(array(
                'customer_id' => 'detail.customer_id',
                'customer_name' => $customerFullname,
                'review_cnt'    => 'COUNT(main_table.review_id)'))
            ->group('detail.customer_id');

        return $this;
    }

    /**
     * Get select count sql
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->_select;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        $countSelect->columns(new Zend_Db_Expr('COUNT(DISTINCT detail.customer_id)'));

        return  $countSelect;
    }
}
