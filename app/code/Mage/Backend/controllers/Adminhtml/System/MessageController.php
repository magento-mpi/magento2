<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_Backend_Adminhtml_System_MessageController extends Mage_Backend_Controller_ActionAbstract
{
    public function listAction()
    {
        $severity = $this->getRequest()->getParam('severity');
        /** @var $messageService Mage_Backend_Model_System_MessagingService */
        $messageCollection = $this->_objectManager
            ->get('Mage_Backend_Model_Resource_System_Message_Collection_Nonsynchronized');
        if ($severity) {
            $messageCollection->setSeverity($severity);
        }
        $result = array();
        foreach ($messageCollection->getItems() as $item) {
            $result[] = array(
                'severity' => $item->getSeverity(), 'text' => $item->getText()
            );
        }
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($result));
    }
}
