<?php
/**
 * Creates a block given failed registration
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Registration;

class Failed extends \Magento\Backend\Block\Template
{
    /**
     * Get error message produced on failure
     *
     * @return string The error message produced upon failure
     */
    public function getSessionError()
    {
        $lastAdded = $this->_backendSession->getMessages(true)->getLastAddedMessage();
        return $lastAdded ? $lastAdded->toString() : null;
    }
}
