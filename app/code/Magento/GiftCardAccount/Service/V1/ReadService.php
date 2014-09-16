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
     * @var \Magento\Sales\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var GiftCardAccountBuilder
     */
    protected $giftCardBuilder;

    /**
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $giftCardHelper;

    /**
     * @param \Magento\Sales\Model\QuoteRepository $quoteRepository
     * @param GiftCardAccountBuilder $giftCardBuilder
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardHelper
     */
    public function __construct(
        \Magento\Sales\Model\QuoteRepository $quoteRepository,
        GiftCardAccountBuilder $giftCardBuilder,
        \Magento\GiftCardAccount\Helper\Data $giftCardHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->giftCardBuilder = $giftCardBuilder;
        $this->giftCardHelper = $giftCardHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
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
