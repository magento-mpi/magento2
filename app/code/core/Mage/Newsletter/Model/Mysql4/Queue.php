<?php
/**
 * Newsletter queue saver
 * 
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */ 
class Mage_Newsletter_Model_Mysql4_Queue extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() 
    {
        $this->_init('newsletter/queue', 'queue_id');
    }
    
    public function addSubscribersToQueue(Mage_Newsletter_Model_Queue $queue, array $subscriberIds) 
    {
    	if (count($subscriberIds)==0) {
    		Mage::throwException('No subscribers selected');
    	}
    	
    	if (!$queue->getId() && $queue->getQueueStatus()!=Mage_Newsletter_Model_Queue::STATUS_NEVER) {
    		Mage::throwException('Invalid queue selected');
    	}
    	
    	$select = $this->getConnection('read')->select();
    	$select->from($this->getTable('queue_link'),'subscriber_id')
    		->where('queue_id = ?', $queue->getId())
    		->where('subscriber_id in (?)', $subscriberIds);
    	
    	$usedIds = $this->getConnection('read')->fetchCol($select);
    	$this->getConnection('write')->beginTransaction();
    	try {
	    	foreach($subscriberIds as $subscriberId) {
	    		if(in_array($subscriberId, $usedIds)) {
	    			continue;
	    		}
	    		$data = array();
	    		$data['queue_id'] = $queue->getId();
	    		$data['subscriber_id'] = $subscriberId;
	    		$this->getConnection('write')->insert($this->getTable('queue_link'), $data);
	    	}
	    	$this->getConnection('write')->commit();
    	} 
    	catch (Exception $e) {
    		$this->getConnection('write')->rollBack();
    	}
    	
    }
    
}
