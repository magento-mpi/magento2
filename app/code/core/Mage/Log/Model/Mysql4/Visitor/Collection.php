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

        $this->_visitorTable = Mage::getSingleton('core/resource')->getTableName('log_resource', 'visitor');
        $this->_urlTable = Mage::getSingleton('core/resource')->getTableName('log_resource', 'url_table');

        $this->_sqlSelect->from($this->_visitorTable);
        $this->_sqlSelect->join( $this->_urlTable, "{$this->_visitorTable}.last_url_id = {$this->_urlTable}.url_id" );

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('log/visitor'));
    }

    /**
     * Enables customer only select
     *
     * @param int $minutes
     * @return object
     */
    public function useOnlineFilter($minutes=15)
    {
        $this->_sqlSelect->where( new Zend_Db_Expr("{$this->_visitorTable}.last_visit_at >= (NOW() - INTERVAL {$minutes} MINUTE)") );
        return $this;
    }
}