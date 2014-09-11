<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Service\V1;

use \Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccountBuilder as GiftCardAccountBuilder;
use \Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccount as GiftCardAccount;

/**
 * Class ReadService
 */
class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var GiftCardAccountBuilder
     */
    protected $giftCardBuilder;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $giftCardHelper;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param GiftCardAccountBuilder $giftCardBuilder
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardHelper
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        GiftCardAccountBuilder $giftCardBuilder,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\GiftCardAccount\Helper\Data $giftCardHelper
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->giftCardBuilder = $giftCardBuilder;
        $this->storeManager = $storeManager;
        $this->giftCardHelper = $giftCardHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);
        $giftCards = $this->giftCardHelper->getCards($quote);
        $cards = [];
        foreach ($giftCards as $giftCard) {
            $cards[] = $giftCard['c'];
        }
        $data = [
            GiftCardAccount::GIFT_CARDS => $cards,
            GiftCardAccount::GIFT_CARDS_AMOUNT => $quote->getGiftCardsAmount(),
            GiftCardAccount::BASE_GIFT_CARDS_AMOUNT => $quote->getBaseGiftCardsAmount(),
            GiftCardAccount::GIFT_CARDS_AMOUNT_USED => $quote->getGiftCardsAmountUsed(),
            GiftCardAccount::BASE_GIFT_CARDS_AMOUNT_USED => $quote->getBaseGiftCardsAmountUsed()
        ];

        $output = $this->giftCardBuilder->populateWithArray($data)->create();
        return $output;
    }
}
