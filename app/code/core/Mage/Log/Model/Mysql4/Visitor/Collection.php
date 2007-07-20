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
     * Visitor data info table name
     *
     * @var string
     */
    protected $_visitorInfoTable;

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
     * Log URL expanded data table name.
     *
     * @var string
     */
    protected $_urlInfoTable;

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
     * Quote data table.
     *
     * @var string
     */
    protected $_quoteTable;

    /**
     * Construct
     *
     */
    function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('log_read'));

        $this->_visitorTable = $resource->getTableName('log/visitor');
        $this->_visitorInfoTable = $resource->getTableName('log/visitor_info');
        $this->_urlTable = $resource->getTableName('log/url_table');
        $this->_urlInfoTable = $resource->getTableName('log/url_info_table');
        $this->_customerTable = $resource->getTableName('log/customer');
        $this->_summaryTable = $resource->getTableName('log/summary_table');
        $this->_summaryTypeTable = $resource->getTableName('log/summary_type_table');
        $this->_quoteTable = $resource->getTableName('log/quote_table');

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
        $this->_sqlSelect->joinLeft( $this->_urlTable, "{$this->_visitorTable}.last_url_id = {$this->_urlTable}.url_id" );
        $this->_sqlSelect->joinLeft( $this->_visitorInfoTable, "{$this->_visitorInfoTable}.visitor_id = {$this->_urlTable}.visitor_id" );
        $this->_sqlSelect->joinLeft( $this->_customerTable, "{$this->_customerTable}.visitor_id = {$this->_urlTable}.visitor_id AND {$this->_customerTable}.logout_at IS NULL" );
        $this->_sqlSelect->joinLeft( $this->_urlInfoTable, "{$this->_urlInfoTable}.url_id = {$this->_urlTable}.url_id" );
        $this->_sqlSelect->joinLeft( $this->_quoteTable, "{$this->_quoteTable}.visitor_id = {$this->_urlTable}.visitor_id" );
        $this->_sqlSelect->where( "{$this->_urlTable}.visit_time >= ( ? - INTERVAL {$minutes} MINUTE)", now() );
        $this->_sqlSelect->group("{$this->_urlTable}.visitor_id");
        return $this;
    }

    public function showCustomersOnly()
    {
        $this->_sqlSelect->where("{$this->_customerTable}.customer_id > 0");
        $this->_sqlSelect->group("{$this->_customerTable}.customer_id");
        return $this;
    }

    public function getAggregatedData($period=720, $type_id=null)
    {
    	$this->_sqlSelect->from($this->_summaryTable);
    	$this->_sqlSelect->where( "{$this->_summaryTable}.add_date >= ( ? - INTERVAL {$period} MINUTE)", now() );
    	if( is_null($type_id) ) {
    		$this->_sqlSelect->where("{$this->_summaryTable}.type_id IS NULL");
    	} else {
			$this->_sqlSelect->where("{$this->_summaryTable}.type_id = ? ", $type_id);
    	}
    	return $this;
    }
}