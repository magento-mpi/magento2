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

class Mage_Newsletter_Model_Mysql4_Queue_Collection extends Mage_Core_Model_Resource_Collection_Abstract 
{
    protected function _construct()
    {
        $this->_init('newsletter/queue');
        
        
    }
    
    public function addTemplateInfo() {
    	$this->getSelect()->joinLeft(array('template'=>$this->getTable('template')),
        						 'template.template_id=main_table.template_id',
        						 array('template_subject','template_sender_name','template_sender_email'));
   		$this->_joinedTables['template'] = true;
   		return $this;
    }
    
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
}
