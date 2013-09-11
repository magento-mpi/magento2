<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\AdminNotification\Controller\Adminhtml\System;

class Message extends \Magento\Backend\Controller\ActionAbstract
{
    public function listAction()
    {
        $severity = $this->getRequest()->getParam('severity');
        $messageCollection = $this->_objectManager
            ->get('Magento\AdminNotification\Model\Resource\System\Message\Collection');
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
            ->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
    }
}
