<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order create errors block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create;

class Messages extends \Magento\Adminhtml\Block\Messages
{
    /**
     * @var \Magento\Adminhtml\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Message $message
     * @param \Magento\Core\Model\Message\CollectionFactory $messageFactory
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Message $message,
        \Magento\Core\Model\Message\CollectionFactory $messageFactory,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        array $data = array()
    ) {
        $this->_sessionQuote = $sessionQuote;
        parent::__construct($context, $coreData, $message, $messageFactory, $data);
    }

    /**
     * @return \Magento\Core\Block\Messages
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->_sessionQuote->getMessages(true));
        parent::_prepareLayout();
    }

}
