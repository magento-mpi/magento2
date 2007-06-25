<?php
/**
 * Log visitor aggregator resource
 *
 * @package     Mage
 * @subpackage  Log
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Log_Model_Mysql4_Visitor_Aggregator
{
    /**
     * Visitor data table name
     *
     * @var string
     */
    protected $_visitorTable;

    /**
     * Customer data table
     *
     * @var string
     */
    protected $_customerTable;

    /**
     * Log URL data table name.
     *
     * @var string
     */
    protected $_urlTable;

    /**
     * Aggregator data table.
     *
     * @var string
     */
    protected $_summaryTable;

    /**
     * Aggregator type data table.
     *
     * @var string
     */
    protected $_summaryTypeTable;

    /**
     * Database read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * Database write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;

    public function __construct()
    {
        $this->_visitorTable = Mage::getSingleton('core/resource')->getTableName('log/visitor');
        $this->_urlTable = Mage::getSingleton('core/resource')->getTableName('log/url_table');
        $this->_customerTable = Mage::getSingleton('core/resource')->getTableName('log/customer');
        $this->_summaryTable = Mage::getSingleton('core/resource')->getTableName('log/summary_table');
        $this->_summaryTypeTable = Mage::getSingleton('core/resource')->getTableName('log/summary_type_table');

        $this->_read = Mage::getSingleton('core/resource')->getConnection('log_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('log_write');
    }

    public function update()
    {
        $types = $this->_getSummaryTypes();
        foreach( $types as $type ) {
            $this->_update($type);
        }
    }

    protected function _getSummaryTypes()
    {
        $types = $this->_read->fetchAll("SELECT type_id, period, period_type FROM {$this->_summaryTypeTable}");
        return $types;
    }

    protected function _update($type)
    {
        $count = $this->_read->fetchOne("SELECT COUNT(summary_id) FROM {$this->_summaryTable} WHERE type_id = ? HAVING (NOW() - INTERVAL {$type['period']} {$type['period_type']}) <= MAX(add_date)", array($type['type_id']));
        if( intval($count) == 0 ) {
            $customers = $this->_read->fetchCol("SELECT visitor_id FROM {$this->_customerTable} WHERE (NOW() - INTERVAL {$type['period']} {$type['period_type']}) <= login_at AND logout_at IS NULL");

            $customerCount = count($customers);

            $customers = ( $customerCount > 0 ) ? $customers : 0;

            $customersCondition = $this->_read->quoteInto('visitor_id NOT IN(?)', $customers);
            $visitorCount = $this->_read->fetchOne("SELECT COUNT(visitor_id) FROM {$this->_visitorTable} WHERE (NOW() - INTERVAL {$type['period']} {$type['period_type']}) <= first_visit_at OR (NOW() - INTERVAL {$type['period']} {$type['period_type']}) <= last_visit_at AND {$customersCondition}");

            $data = array(
                    'type_id' => $type['type_id'],
                    'visitor_count' => $visitorCount,
                    'customer_count' => $customerCount,
                    'add_date' => new Zend_Db_Expr('NOW()')
                    );
            $this->_write->insert($this->_summaryTable, $data);
        }
    }
}