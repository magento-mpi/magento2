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
     * @var \Magento\Message\Manager
     */
    protected $messageManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Message\Manager $messageManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Message\Manager $messageManager,
        array $data = array()
    ) {
        $this->messageManager = $messageManager;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Get error message produced on failure
     *
     * @return string The error message produced upon failure
     */
    public function getSessionError()
    {
        $lastAdded = $this->messageManager->getMessages(true)->getLastAddedMessage();
        return $lastAdded ? $lastAdded->toString() : null;
    }
}
