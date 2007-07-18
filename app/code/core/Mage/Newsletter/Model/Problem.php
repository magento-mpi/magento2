<?php
/**
 * Nesletter problem model
 *
 * @package    Mage
 * @subpackage Newsletter
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Newsletter_Model_Problem extends Mage_Core_Model_Abstract
{
	protected  $_subscriber = null;
	
	protected function _construct() 
	{
		$this->_init('newsletter/problem');
	}
	
	public function addSubscriberData(Mage_Newsletter_Model_Subscriber $subscriber) 
	{
		$this->setSubscriberId($subscriber->getId());
	}
	
	public function addQueueData(Mage_Newsletter_Model_Queue $queue) 
	{
		$this->setQueueId($queue->getId());
	}
	
	public function addErrorData(Exception $e) 
	{
		$this->setProblemErrorCode($e->getCode());
		$this->setProblemErrorText($e->getMessage());
	}
	
	public function getSubscriber() 
	{
		if(!$this->getSubscriberId()) {
			return null;
		}
		
		
		if(is_null($this->_subscriber)) {
			$this->_subscriber = Mage::getModel('newsletter/subscriber')
				->load($this->getSubscriberId());
		}
		
		return $this->_subscriber;
	}
	
	public function unsubscribe() 
	{
		if($this->getSubscriber()) {
			$this->getSubscriber()->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED)
				->setIsStatusChanged(true)
				->save();
		}
	}
}// Class Mage_Newsletter_Model_Problem END