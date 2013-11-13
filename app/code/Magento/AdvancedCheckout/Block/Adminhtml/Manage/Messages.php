<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Checkout block for showing messages
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage;

class Messages extends \Magento\Adminhtml\Block\Messages
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Message $message
     * @param \Magento\Core\Model\Message\CollectionFactory $messageFactory
     * @param \Magento\Backend\Model\Session $backendSession
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Message $message,
        \Magento\Core\Model\Message\CollectionFactory $messageFactory,
        \Magento\Backend\Model\Session $backendSession,
        array $data = array()
    ) {
        $this->_backendSession = $backendSession;
        parent::__construct($context, $coreData, $message, $messageFactory, $data);
    }

    /**
     * Prepares layout for current block
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->_backendSession->getMessages(true));
        parent::_prepareLayout();
    }
}
