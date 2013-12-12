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

class Messages extends \Magento\View\Element\Messages
{
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $sessionQuote;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Message\Factory $messageFactory
     * @param \Magento\Message\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Message\Factory $messageFactory,
        \Magento\Message\CollectionFactory $collectionFactory,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        array $data = array()
    ) {
        $this->sessionQuote = $sessionQuote;
        parent::__construct($context, $messageFactory, $collectionFactory, $data);
    }

    /**
     * @return \Magento\View\Element\Messages
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->sessionQuote->getMessages(true));
        parent::_prepareLayout();
    }

}
