<?php
/**
 * Newsletter Subscribers Collection
 * 
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Newsletter_Model_Mysql4_Subscriber_Collection extends Varien_Data_Collection_Db
{   
    /**
     * Subscribers table name
     *
     * @var string
     */
    protected $_subscriberTable;
    
    /**
     * Customers table name
     *
     * @var string
     */
    protected $_customersTable;
    
     /**
     * Queue link table name
     *
     * @var string
     */
    protected $_queueLinkTable;
    
    /**
     * Constructor
     *
     * Configures collection
     */
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('newsletter_read'));
        $this->_subscriberTable = Mage::getSingleton('core/resource')->getTableName('newsletter/subscriber');
        $this->_queueLinkTable = Mage::getSingleton('core/resource')->getTableName('newsletter/queue_link');
        $this->_customerTable = Mage::getSingleton('core/resource')->getTableName('customer/customer');
        $this->_sqlSelect->from($this->_subscriberTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('newsletter/subscriber'));
    }
    
    /**
     * Set loading mode subscribers by queue
     * 
     * @param Mage_Newsletter_Model_Queue $queue
     */
    public function useQueue(Mage_Newsletter_Model_Queue $queue)
    {
        $this->_sqlSelect->join($this->_queueLinkTable, array(), "{$this->_queueLinkTable}.subscriber_id = {$this->_subscriberTable}.subscriber_id")
            ->where("{$this->_queueLinkTable}.queue_id = ? ", $queue->getId());
            
        return $this;
    }
    
    /**
     * Set loading mode subscribers by queue
     * 
     * @param   int $websiteId
     */
    public function useWebsite($websiteId)
    {
        $this->_sqlSelect->where("{$this->_subscriberTable}.website_id = ?", $websiteId);
   
        return $this;
    }
    
    /**
     * Show customer info too
     */
    public function showCustomersInfo( )
    {
        $this->_sqlSelect->joinLeft($this->_customerTable, 
                                    "{$this->_customerTable}.customer_id = {$this->_subscriberTable}.customer_id",
                                    array('firstname','lastname'));
        
        return $this;
    }
    
    /**
     * Load only subscribed customers
     */
    public function useOnlyCustomers()
    {
        $this->_sqlSelect->where("{$this->_subscriberTable}.customer_id > 0");
        
        return $this;
    }
    
    /**
     * Show only with subscribed status
     */
    public function useOnlySubscribed() 
    {
        $this->_sqlSelect->where("{$this->_subscriberTable}.status = ?", Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
        
        return $this;
    }
}