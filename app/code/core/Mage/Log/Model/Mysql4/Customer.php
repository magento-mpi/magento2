<?php
/**
 * Customer log resource
 *
 * @package     Mage
 * @subpackage  Log
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Log_Model_Mysql4_Customer
{
    /**
     * Visitor data table name
     *
     * @var string
     */
    protected $_visitorTable;

    /**
     * Visitor info data table
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
     * Url info data table
     *
     * @var string
     */
    protected $_urlInfoTable;

    /**
     * Log URL data table name.
     *
     * @var string
     */
    protected $_urlTable;

    /**
     * Log quote data table name.
     *
     * @var string
     */
    protected $_quoteTable;

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

    protected $_visitorId;

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');

        $this->_visitorTable = $resource->getTableName('log/visitor');
        $this->_visitorInfoTable = $resource->getTableName('log/visitor_info');
        $this->_urlTable = $resource->getTableName('log/url_table');
        $this->_urlInfoTable = $resource->getTableName('log/url_info_table');
        $this->_customerTable = $resource->getTableName('log/customer');
        $this->_quoteTable = $resource->getTableName('log/quote_table');

        $this->_read = $resource->getConnection('log_read');
        $this->_write = $resource->getConnection('log_write');
    }

    public function getLastActivity($model)
    {
        $select = $this->_read->select();
        $select->from($this->_urlTable);
        $select->joinLeft($this->_urlInfoTable, "{$this->_urlTable}.url_id = {$this->_urlInfoTable}.url_id");
        $select->joinLeft($this->_customerTable, "{$this->_urlTable}.visitor_id = {$this->_customerTable}.visitor_id");
        $select->where("{$this->_customerTable}.customer_id = {$model->getId()}");
        $select->order("{$this->_urlTable}.url_id DESC");

        $lastActivity = $this->_write->fetchRow($select);
        $model->setLastActivity($lastActivity);
    }

    public function getLogTime($model)
    {
        $select = $this->_read->select();
        $select->from($this->_customerTable);
        $select->where("{$this->_customerTable}.customer_id = {$model->getId()}");
        $select->order("{$this->_customerTable}.log_id DESC");

        $lastLog = $this->_write->fetchRow($select);
        $model->setLastLog($lastLog);
    }

    public function getOnlineStatus($model, $timeout=15)
    {
        $timeExpr = new Zend_Db_Expr("NOW() - INTERVAL {$timeout} MINUTE < {$this->_urlTable}.visit_time");

        $select = $this->_read->select();
        $select->from($this->_urlTable, array('url_id'));
        $select->joinLeft($this->_customerTable, "{$this->_urlTable}.visitor_id = {$this->_customerTable}.visitor_id", array());
        $select->where("{$this->_customerTable}.customer_id = {$model->getId()} AND {$timeExpr}");
        $select->order("{$this->_urlTable}.url_id DESC");

        $onlineStatus = $this->_write->fetchRow($select);
        $model->setIsOnline( (intval($onlineStatus['url_id']) > 0) ? true : false );
    }

    public function getLastQuote($model)
    {
        $select = $this->_read->select();
        $select->from($this->_quoteTable);
        $select->joinLeft($this->_customerTable, "{$this->_quoteTable}.visitor_id = {$this->_customerTable}.visitor_id");
        $select->where("{$this->_customerTable}.customer_id = {$model->getId()}");
        $select->order("{$this->_quoteTable}.quote_id DESC");

        $lastQuote = $this->_write->fetchRow($select);
        $model->setLastQuote($lastQuote);
    }
}