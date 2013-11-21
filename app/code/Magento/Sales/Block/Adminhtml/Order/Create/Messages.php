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
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Message\Factory $messageFactory
     * @param \Magento\Message\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Core\Helper\Data $coreData,
        \Magento\View\Block\Template\Context $context,
        \Magento\Message\Factory $messageFactory,
        \Magento\Message\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->sessionQuote = $sessionQuote;
        parent::__construct($coreData, $context, $messageFactory, $collectionFactory, $data);
    }

    /**
     * @return \Magento\View\Block\Messages
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->sessionQuote->getMessages(true));
        parent::_prepareLayout();
    }

}
