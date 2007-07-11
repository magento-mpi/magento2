<?php 
/**
 * Newsletter subscribe controller 
 *
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */ 
 class Mage_Newsletter_IndexController extends Mage_Core_Controller_Front_Action 
 {
    public function indexAction() {
        $queue = Mage::getModel('newsletter/queue');
        $queue->setTemplateId(1);
        $queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_NEVER);
        $queue->save();
    }
 }