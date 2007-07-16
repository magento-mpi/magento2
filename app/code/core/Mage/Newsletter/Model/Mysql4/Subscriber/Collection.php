<?php
/**
 * Newsletter Subscribers Collection
 * 
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 * @todo        Refactoring this collection to customers new structure.
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
     * Queue link table name
     *
     * @var string
     */
    protected $_queueLinkTable;
    
    /**
     * Queue joined flag
     *
     * @var boolean
     */
    protected $_queueJoinedFlag = false;
    
    /**
     * Flag that indicates apply of customers info on load
     *
     * @var boolean
     */
    protected $_showCustomersInfo = false;
    
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
        $this->_sqlSelect->from(array('main_table'=>$this->_subscriberTable));
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('newsletter/subscriber'));
    }
    
    /**
     * Set loading mode subscribers by queue
     * 
     * @param Mage_Newsletter_Model_Queue $queue
     */
    public function useQueue(Mage_Newsletter_Model_Queue $queue)
    {
        $this->_sqlSelect->join(array('link'=>$this->_queueLinkTable), "link.subscriber_id = main_table.subscriber_id", array())
            ->where("link.queue_id = ? ", $queue->getId());
        $this->_queueJoinedFlag = true;
        return $this;
    }    
    
    
        
    /**
     * Set using of links to only unsendet letter subscribers.
     */ 
    public function useOnlyUnsent( )
    {
        if($this->_queueJoinedFlag) {
            $this->_sqlSelect->where("link.letter_sent_at IS NULL");
        }
        
        return $this;
    }
    
    /**
     * Loads customers info to collection
     *
     */
    protected function _addCustomersData( )
    {
        $customersIds = array();
        
        foreach ($this->getItems() as $item) {
        	if($item->getCustomerId()) {
        		$customersIds[] = $item->getCustomerId();
        	}
        }
        
        if(count($customersIds) == 0) {
        	return;
        }
                
        $customers = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
			->addAttributeToFilter('entity_id', array("in"=>$customersIds));
        
        $customers->load();
		
        foreach($customers->getItems() as $customer) {
        	$subscriber = $this->getItemByColumnValue('customer_id', $customer->getId());
        	$subscriber->setCustomerName($customer->getName())
        		->setCustomerFirstName($customer->getFirstName())
        		->setCustomerLastName($customer->getLastName());        	
        }
                
    }
    
    /**
     * Sets flag for customer info loading on load
     *
     * @param   boolean $show
     * @return  Mage_Newsletter_Model_Mysql4_Subscriber_Collection
     */
    public function showCustomerInfo($show=true) 
    {
    	$this->_showCustomersInfo = (boolean) $show;
    	return $this;
    }
    
    /**
     * Load only subscribed customers
     */
    public function useOnlyCustomers()
    {
        $this->_sqlSelect->where("main_table.customer_id > 0");
        
        return $this;
    }
    
    /**
     * Show only with subscribed status
     */
    public function useOnlySubscribed() 
    {
        $this->_sqlSelect->where("main_table.status = ?", Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
        
        return $this;
    }
    
    /**
     * Load subscribes to collection
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Varien_Data_Collection_Db
     */
    public function load($printQuery=false, $logQuery=false) 
    {
    	parent::load($printQuery, $logQuery);
    	if($this->_showCustomersInfo) {
    		$this->_addCustomersData();
    	}
    	return $this;
    }
}