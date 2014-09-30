<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping order create info content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Order\Create\Info;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Content extends \Magento\GiftWrapping\Block\Adminhtml\Order\Create\Info
{
    /**
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $messageHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Core\Helper\Data $coreData,
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        array $data = array()
    ) {
        $this->messageHelper = $messageHelper;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $coreData,
            $giftWrappingData,
            $wrappingCollectionFactory,
            $data
        );
    }

    /**
     * @return $this|void
     */
    protected function _beforeToHtml()
    {
        if (!$this->messageHelper->getIsMessagesAvailable('main', $this->getQuote(), $this->getStoreId())) {
            $this->_template = 'order/create/info/content.phtml';
        }
    }
}
