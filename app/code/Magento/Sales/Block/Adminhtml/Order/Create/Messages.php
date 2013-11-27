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
    protected $sessionQuote;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Message\Factory $messageFactory
     * @param \Magento\Message\CollectionFactory $collectionFactory
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Message\Factory $messageFactory,
        \Magento\Message\CollectionFactory $collectionFactory,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        array $data = array()
    ) {
        $this->sessionQuote = $sessionQuote;
        parent::__construct($context, $coreData, $messageFactory, $collectionFactory, $data);
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
