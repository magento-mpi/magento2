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
 * @package    Mage_CustomerAlert
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Alerts queue saver
 * 
 * @category   Mage
 * @package    Mage_CustomerAlert
 * @author     Vasily Selivanov <vasily@varien.com>
 */ 

class Mage_CustomerAlert_Model_Mysql4_Queue extends Mage_Newsletter_Model_Mysql4_Queue {
    
    public function __construct()
    {
        $this->_init('customeralert/alert', 'queue_id');
    }
    
    public function addSubscribersToQueue(Mage_Newsletter_Model_Queue $queue, array $subscriberIds) 
    {
        
        if (count($subscriberIds)==0) {
            Mage::throwException(__('No subscribers selected'));
        }
        
        if (!$queue->getId() && $queue->getQueueStatus()!=Mage_Newsletter_Model_Queue::STATUS_NEVER) {
            Mage::throwException(__('Invalid queue selected'));
        }
        $select = $this->getConnection('read')->select();
        $select->from($this->getTable('alert/queue_link'),'subscriber_id')
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
?>