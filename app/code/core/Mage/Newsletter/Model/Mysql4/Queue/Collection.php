<?php
/**
 * Newsletter queue collection.
 * 
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */ 

class Mage_Newsletter_Model_Mysql4_Queue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
	/**
	 * Initializes collection
	 */
    protected function _construct()
    {
        $this->_init('newsletter/queue');        
    }
    
    /**
     * Joines templates information
     *
     * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function addTemplateInfo() {
    	$this->getSelect()->joinLeft(array('template'=>$this->getTable('template')),
        						 'template.template_id=main_table.template_id',
        						 array('template_subject','template_sender_name','template_sender_email'));
   		$this->_joinedTables['template'] = true;
   		return $this;
    }
    
    /**
     * Joines subscribers information
     *
     * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function addSubscribersInfo() 
    {
    	$this->getSelect()
    		->joinLeft(array('link_total'=>$this->getTable('queue_link')),
    								 'main_table.queue_id=link_total.queue_id', 
    								 array(
    								 	new Zend_Db_Expr('COUNT(link_total.queue_link_id) AS subscribers_total')
    								 ))
 			->joinLeft(array('link_sent'=>$this->getTable('queue_link')),
    								 'main_table.queue_id=link_sent.queue_id and link_sent.letter_sent_at IS NOT NULL', 
    								 array(
    								 	new Zend_Db_Expr('COUNT(link_sent.queue_link_id) AS subscribers_sent')
    								 ))
    		->group('main_table.queue_id');
    	
    	return $this;
    }
    
    /**
     * Set filter for queue by subscriber.
     *
     * @param 	int		$subscriberId
     * @return 	Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function addSubscriberFilter($subscriberId) 
    {
    	$this->getSelect()
    		->join(array('link'=>$this->getTable('queue_link')),
    								 'main_table.queue_id=link.queue_id',
    								 array('letter_sent_at')
    								 )
 			->where('link.subscriber_id = ?', $subscriberId);
    	
    	return $this;
    }
    
    /**
     * Add filter by only ready fot sending item
     *
     * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function addOnlyForSendingFilter() 
    {
    	$this->getSelect()
    		->where('main_table.queue_status in (?)', array(Mage_Newsletter_Model_Queue::STATUS_SENDING, 
    														Mage_Newsletter_Model_Queue::STATUS_NEVER))
    		->where('main_table.queue_start_at < ?', now())
    		->where('main_table.queue_start_at IS NOT NULL');
    	
    	return $this;
    }
    
    /**
     * Add filter by only not sent items
     *
     * @return Mage_Newsletter_Model_Mysql4_Queue_Collection
     */
    public function addOnlyUnsentFilter() 
    {
    	$this->getSelect()
    		->where('main_table.queue_status = ?',	Mage_Newsletter_Model_Queue::STATUS_NEVER);
    	
   		return $this;
    }
    
    public function toOptionArray()
    {
        return $this->_toOptionArray('queue_id', 'template_subject');
    }
    
}
