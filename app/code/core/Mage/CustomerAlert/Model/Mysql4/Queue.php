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

class Mage_CustomerAlert_Model_Mysql4_Queue extends Mage_Core_Model_Mysql4_Abstract {
    
    public function __construct()
    {
        $this->_init('customeralert/queue', 'queue_id');
    }
    
    public function addCustomersToAlertQueue(Mage_CustomerAlert_Model_Queue $queue, Mage_CustomerAlert_Model_Mysql4_Customer_Collection $customers) 
    {
        if (!$customers || count($customers->getItems())==0) {
            Mage::throwException(__('No subscribers selected'));
        }
        $queue->save();
        if (!$queue->getId()) {
            Mage::throwException(__('Invalid queue selected'));
        }
        $this->getConnection('write')->beginTransaction();
        $data = array();
        $data['queue_id'] = $queue->getId();
        $data['queue_created_at'] = now();
        try {
            foreach($customers->getItems() as $customer) {
                $this->getConnection('write')->insert($this->getTable('queue_link'), $data);
            }
            $this->getConnection('write')->commit();
        } catch (Exception $e) {
            $this->getConnection('write')->rollBack();
        }
        
    }
    
}
?>