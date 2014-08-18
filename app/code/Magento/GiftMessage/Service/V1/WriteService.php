<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Service\V1;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Framework\Exception\NoSuchEntityException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;


    /**
     * @var \Magento\GiftMessage\Model\GiftMessage
     */
    protected $messageInitializer;

    /**
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Service\V1\Product\ProductLoader
     */
    protected $productLoader;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\GiftMessage\Model\GiftMessage $initializer
     * @param \Magento\GiftMessage\Helper\Message $helper
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\GiftMessage\Model\GiftMessage $initializer,
        \Magento\GiftMessage\Helper\Message $helper,
        \Magento\Catalog\Service\V1\Product\ProductLoader $productLoader
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->storeManager = $storeManager;
        $this->messageInitializer = $initializer;
        $this->productLoader = $productLoader;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function setForQuote($cartId,  \Magento\GiftMessage\Service\V1\Data\Message $giftMessage)
    {
        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);

        if (0 == $quote->getItemsCount()) {
            throw new InputException('Gift Messages is not applicable for empty cart');
        }

        if ($quote->isVirtual()) {
            throw new InvalidTransitionException('Gift Messages is not applicable for virtual products');
        }

        try {
            $this->setMessage($quote, 'quote', $entityId = null, $giftMessage);
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not add gift message to shopping cart');
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setForItem($cartId,  \Magento\GiftMessage\Service\V1\Data\Message $giftMessage, $itemId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);

        if (!$item = $quote->getItemById($itemId)) {
            throw new NoSuchEntityException('There is no product with provided SKU in the cart');
        };

        if ($item->getIsVirtual()) {
            throw new InvalidTransitionException('Gift Messages is not applicable for virtual products');
        }

        $this->setMessage($quote, 'quote_item', $itemId, $giftMessage);
        return true;
    }


    /**
     * Set gift message to item or quote
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @param $type
     * @param null $entityId
     * @param \Magento\GiftMessage\Service\V1\Data\Message $giftMessage
     * @return $this
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException
     */
    protected function setMessage(\Magento\Sales\Model\Quote $quote, $type, $entityId = null, $giftMessage)
    {
        if (is_null($quote->getBillingAddress()->getCountryId())) {
            throw new InvalidTransitionException('Billing address is not set');
        }

        // check if shipping address is set
        if (is_null($quote->getShippingAddress()->getCountryId())) {
            throw new InvalidTransitionException('Shipping address is not set');
        }

        $configType = $type == 'quote_item'?'':'items';
        if(!$this->helper->getIsMessagesAvailable($configType, $quote, $this->storeManager->getStore()))
        {
            throw new CouldNotSaveException('Gift Message is not available');
        }
        $message[$type][$entityId] = [
            'from' => $giftMessage->getSender(),
            'to' => $giftMessage->getRecipient(),
            'message' => $giftMessage->getMessage(),
            'type' => 'quote'
        ];

        try {
        $this->messageInitializer->create($message, $quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not add gift message to shopping cart');
        }
    }
}
