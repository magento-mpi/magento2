<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage;

/**
 * Admin Checkout block for showing messages
 */
class Messages extends \Magento\View\Block\Messages
{
    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Message\Factory $messageFactory
     * @param \Magento\Message\CollectionFactory $collectionFactory
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Message\Factory $messageFactory,
        \Magento\Message\CollectionFactory $collectionFactory,
        \Magento\Message\ManagerInterface $messageManager,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $messageFactory, $collectionFactory, $messageManager, $data);
    }

    /**
     * Prepares layout for current block
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->messageManager->getMessages(true));
        parent::_prepareLayout();
    }
}
