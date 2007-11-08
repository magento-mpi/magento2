<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log visitor aggregator resource
 *
 * @category   Mage
 * @package    Mage_Log
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
        $count = $this->_read->fetchOne("SELECT COUNT(summary_id) FROM {$this->_summaryTable} WHERE type_id = ? HAVING (".now()." - INTERVAL {$type['period']} {$type['period_type']}) <= MAX(add_date)", array($type['type_id']));
        if( intval($count) == 0 ) {
			$customers = $this->_read->fetchCol("SELECT visitor_id FROM {$this->_customerTable} WHERE (".now()." - INTERVAL {$type['period']} {$type['period_type']}) <= login_at AND logout_at IS NULL");

            $customerCount = count($customers);

            $customers = ( $customerCount > 0 ) ? $customers : 0;

            $customersCondition = $this->_read->quoteInto('visitor_id NOT IN(?)', $customers);
            $visitorCount = $this->_read->fetchOne("SELECT COUNT(visitor_id) FROM {$this->_visitorTable} WHERE (".now()." - INTERVAL {$type['period']} {$type['period_type']}) <= first_visit_at OR (NOW() - INTERVAL {$type['period']} {$type['period_type']}) <= last_visit_at AND {$customersCondition}");

            if( $customerCount == 0 && $visitorCount == 0 ) {
                return;
            }

            $data = array(
                    'type_id' => $type['type_id'],
                    'visitor_count' => $visitorCount,
                    'customer_count' => $customerCount,
                    'add_date' => now()
                    );
            $this->_write->insert($this->_summaryTable, $data);
        }
    }

    public function updateOneshot($minutes=60, $interval=300)
    {
    	$last_update = $this->_read->fetchOne("SELECT UNIX_TIMESTAMP(MAX(add_date)) FROM {$this->_summaryTable} WHERE type_id IS NULL");
    	$next_update = $last_update + $interval;

    	if( time() >= $next_update ) {
	        $stats = $this->_read->fetchAssoc("SELECT
	        								u.visit_time,
	                                    	v.visitor_id,
	                                    	c.customer_id,
	                                    	ROUND( (UNIX_TIMESTAMP(u.visit_time) - UNIX_TIMESTAMP(".now()." - INTERVAL {$minutes} MINUTE )) / {$interval} )  as _diff,
	                                    	COUNT(DISTINCT(v.visitor_id)) as visitor_count,
	                                    	COUNT(DISTINCT(c.customer_id)) as customer_count
	                                    FROM
	                                    	{$this->_urlTable} u
	                                    LEFT JOIN {$this->_visitorTable} v ON(v.visitor_id = u.visitor_id)
	                                    LEFT JOIN {$this->_customerTable} c on(c.visitor_id = v.visitor_id)
	                                    WHERE
	                                    	UNIX_TIMESTAMP(u.visit_time) > {$next_update}
	                                    group by _diff");

			foreach( $stats as $stat ) {
		        $data = array(
		        	'type_id' => new Zend_Db_Expr('NULL'),
		        	'visitor_count' => $stat['visitor_count'],
		        	'customer_count' => $stat['customer_count'],
		        	'add_date' => $stat['visit_time']
		        );
				$this->_write->insert($this->_summaryTable, $data);
			}
    	}

    }
}