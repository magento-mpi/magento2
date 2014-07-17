<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Tax;

use Magento\Framework\Exception\InputException;

class IgnoreTaxNotification extends \Magento\Tax\Controller\Adminhtml\Tax
{

    /**
     * Set tax ignore notification flag and redirect back
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $section = $this->getRequest()->getParam('section');
        if ($section) {
            try {
                $path = 'tax/notification/ignore_' . $section;
                $this->_objectManager->get('\Magento\Core\Model\Resource\Config')->saveConfig($path, 1, \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT, 0);
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
    }
}
