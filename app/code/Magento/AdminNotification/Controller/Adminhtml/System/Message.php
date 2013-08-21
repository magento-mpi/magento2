<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_AdminNotification_Controller_Adminhtml_System_Message extends Magento_Backend_Controller_ActionAbstract
{
    public function listAction()
    {
        $severity = $this->getRequest()->getParam('severity');
        $messageCollection = $this->_objectManager
            ->get('Magento_AdminNotification_Model_Resource_System_Message_Collection');
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
            ->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
    }
}
