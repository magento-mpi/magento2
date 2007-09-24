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
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter queue saver
 * 
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */ 
class Mage_Newsletter_Model_Mysql4_Queue extends Mage_Core_Model_Mysql4_Abstract
{
	
    protected function _construct() 
    {
        $this->_init('newsletter/queue', 'queue_id');
    }
       
    /**
     * Add subscribers to queue
     *
     * @param Mage_Newsletter_Model_Queue $queue
     * @param array $subscriberIds
     */
    public function addSubscribersToQueue(Mage_Newsletter_Model_Queue $queue, array $subscriberIds) 
    {
    	if (count($subscriberIds)==0) {
    		Mage::throwException(__('No subscribers selected'));
    	}
    	
    	if (!$queue->getId() && $queue->getQueueStatus()!=Mage_Newsletter_Model_Queue::STATUS_NEVER) {
    		Mage::throwException(__('Invalid queue selected'));
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
    
    public function removeSubscribersFromQueue(Mage_Newsletter_Model_Queue $queue)
    {
    	try {
	    	$this->getConnection('write')->delete(
	    		$this->getTable('queue_link'), 
	    		array(
	    			$this->getConnection('write')->quoteInto('queue_id = ?', $queue->getId()),
	    			'letter_sent_at IS NULL'
	    		)
	    	);
	    	
	    	$this->getConnection('write')->commit();
    	} 
    	catch (Exception $e) {
    		$this->getConnection('write')->rollBack();
    	}
    	
    }
    
    public function setStores(Mage_Newsletter_Model_Queue $queue) 
    {
    	$this->getConnection('write')
    		->delete(
    			$this->getTable('queue_store_link'), 
    			$this->getConnection('write')->quoteInto('queue_id = ?', $queue->getId())
    		);
    	
    	if (!is_array($queue->getStores()))	{ 
    		$stores = array(); 
    	} else {
    		$stores = $queue->getStores();
    	}
    	
    	foreach ($stores as $storeId) {
    		$data = array();
    		$data['store_id'] = $storeId;
    		$data['queue_id'] = $queue->getId();
    		$this->getConnection('write')->insert($this->getTable('queue_store_link'), $data);
    	}
    	 
		$this->removeSubscribersFromQueue($queue);

		if(count($stores)==0) {
			return $this;
		}
		$subscribers = Mage::getResourceSingleton('newsletter/subscriber_collection')
			->addFieldToFilter('store_id', array('in'=>$stores))
			->useOnlySubscribed()
			->load();
		 
		$subscriberIds = array();
		
		foreach ($subscribers as $subscriber) {
			$subscriberIds[] = $subscriber->getId();
		}
		
		if (count($subscriberIds) > 0) {
			$this->addSubscribersToQueue($queue, $subscriberIds);
		}
		
    	return $this;
    }
    
    public function getStores(Mage_Newsletter_Model_Queue $queue) 
    {
    	$select = $this->getConnection('read')->select()
    		->from($this->getTable('queue_store_link'), 'store_id')
    		->where('queue_id = ?', $queue->getId());
    	
    	if(!($result = $this->getConnection('read')->fetchCol($select))) {
    		$result = array();
    	}
    	
    	return $result;
    }
    
    /**
     * Saving template after saving queue action
     *
     * @param Mage_Core_Model_Abstract $queue
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $queue) 
    {
    	if($queue->getSaveTemplateFlag()) {
    		$queue->getTemplate()->save();
    	}
    	
    	if($queue->getSaveStoresFlag()) {
    		$this->setStores($queue);    		
    	}
    	
    	return $this;
    }
    
}
