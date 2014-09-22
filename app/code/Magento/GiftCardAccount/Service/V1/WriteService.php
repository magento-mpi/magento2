<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Service\V1;

use \Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccountBuilder as GiftCardAccountBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Class WriteService
 */
class WriteService implements WriteServiceInterface
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
     * @var \Magento\GiftCardAccount\Service\V1\GiftCardAccountLoader
     */
    protected $giftCardLoader;

    /**
     * @param \Magento\Sales\Model\QuoteRepository $quoteRepository
     * @param GiftCardAccountBuilder $giftCardBuilder
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardHelper
     * @param GiftCardAccountLoader $loader
     */
    public function __construct(
        \Magento\Sales\Model\QuoteRepository $quoteRepository,
        GiftCardAccountBuilder $giftCardBuilder,
        \Magento\GiftCardAccount\Helper\Data $giftCardHelper,
        \Magento\GiftCardAccount\Service\V1\GiftCardAccountLoader $loader
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->giftCardBuilder = $giftCardBuilder;
        $this->giftCardHelper = $giftCardHelper;
        $this->giftCardLoader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, \Magento\GiftCardAccount\Service\V1\Data\Cart\GiftCardAccount $giftCardAccountData)
    {
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException("Cart $cartId doesn't contain products");
        }
        $cardCode = $giftCardAccountData->getGiftCards();
        $giftCard = $this->giftCardLoader->load(array_shift($cardCode));
        try {
            $giftCard->addToCart(true, $quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not add gift card code');
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($cartId, $couponCode)
    {
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException("Cart $cartId doesn't contain products");
        }
        $giftCard = $this->giftCardLoader->load($couponCode);

        try {
            $giftCard->removeFromCart(true, $quote);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException('Could not delete gift card from quote');
        }
        return true;
    }
}
