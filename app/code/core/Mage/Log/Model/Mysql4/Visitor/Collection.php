<?php
/**
 * Mage_Log_Model_Mysql4_Customers_Collection
 *
 * @package     package
 * @subpackage  subpackage
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Log_Model_Mysql4_Visitor_Collection extends Varien_Data_Collection_Db
{
    /**
     * Visitor data table name
     *
     * @var string
     */
    protected $_visitorTable;

    /**
     * Log URL data table name.
     *
     * @var string
     */
    protected $_urlTable;

    /**
     * Construct
     *
     */
    function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('log_read'));

        $this->_visitorTable = Mage::getSingleton('core/resource')->getTableName('log/visitor');
        $this->_urlTable = Mage::getSingleton('core/resource')->getTableName('log/url_table');

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('log/visitor'));
    }

    /**
     * Enables online only select
     *
     * @param int $minutes
     * @return object
     */
    public function useOnlineFilter($minutes=15)
    {
        $this->_sqlSelect->from($this->_visitorTable);
        $this->_sqlSelect->join( $this->_urlTable, "{$this->_visitorTable}.last_url_id = {$this->_urlTable}.url_id" );
        $this->_sqlSelect->where( new Zend_Db_Expr("{$this->_visitorTable}.last_visit_at >= (NOW() - INTERVAL {$minutes} MINUTE)") );
        return $this;
    }

    public function getStatistics($measure='d')
    {
        switch( $measure ) {
            case 'd':
                $measureDateFormat = '%Y-%m-%d';
                break;

            case 'h':
                $measureDateFormat = '%Y-%m-%d %h:%i';
                break;
        }
        $this->_sqlSelect->from($this->_visitorTable, new Zend_Db_Expr("COUNT( `first_visit_at` ) AS first_visit_at_count, DATE_FORMAT( first_visit_at, '{$measureDateFormat}' ) as first_visit_at_date"));
        $this->_sqlSelect->group('first_visit_at_date');
        return $this;
    }

    public function getTimeline($hoursCount=12)
    {
        $this->_sqlSelect->from($this->_visitorTable, new Zend_Db_Expr("DATE_FORMAT( first_visit_at, '%Y-%m-%d %H' ) as first_visit_at_date, IF ((customer_id > 0), COUNT(customer_id), 0) as customers, IF ((customer_id = 0), COUNT(customer_id), 0) as visitors"));
        $this->_sqlSelect->where( new Zend_Db_Expr("NOW() - INTERVAL {$hoursCount} HOUR <= first_visit_at") );
        $this->_sqlSelect->group('first_visit_at_date');
        return $this;
    }

    /**
     * Enables customer only select
     *
     * @access public
     * @return void
     */
    public function showCustomersOnly()
    {
        $this->_sqlSelect->from($this->_visitorTable);
        $this->_sqlSelect->join( $this->_urlTable, "{$this->_visitorTable}.last_url_id = {$this->_urlTable}.url_id" );
        $this->_sqlSelect->where( "{$this->_visitorTable}.customer_id > 0" );
        return $this;
    }

    /**
     * Enables guests only select
     *
     * @access public
     * @return void
     */
    public function showGuestsOnly()
    {
        $this->_sqlSelect->from($this->_visitorTable);
        $this->_sqlSelect->join( $this->_urlTable, "{$this->_visitorTable}.last_url_id = {$this->_urlTable}.url_id" );
        $this->_sqlSelect->where( "{$this->_visitorTable}.customer_id = 0" );
        return $this;
    }

    public function applyDateRange($dateFrom, $dateTo)
    {
        $this->_sqlSelect->where( new Zend_Db_Expr("{$this->_visitorTable}.last_visit_at BETWEEN '{$dateFrom}' AND '{$dateTo}' ") );
        return $this;
    }
}