<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Coupon;

use \Magento\Checkout\Service\V1\Data\Cart\CouponBuilder as CouponBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

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
     * @var CouponBuilder
     */
    protected $couponBuilder;

    /**
     * @param \Magento\Sales\Model\QuoteRepository $quoteRepository
     * @param CouponBuilder $couponBuilder
     */
    public function __construct(
        \Magento\Sales\Model\QuoteRepository $quoteRepository,
        CouponBuilder $couponBuilder
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->couponBuilder = $couponBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, \Magento\Checkout\Service\V1\Data\Cart\Coupon $couponCodeData)
    {
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException("Cart $cartId doesn't contain products");
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $couponCode = trim($couponCodeData->getCouponCode());

        try {
            $quote->setCouponCode($couponCode);
            $quote->collectTotals()->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not apply coupon code');
        }
        if ($quote->getCouponCode() != $couponCode) {
            throw new NoSuchEntityException('Coupon code is not valid');
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($cartId)
    {
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException("Cart $cartId doesn't contain products");
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            $quote->setCouponCode('');
            $quote->collectTotals()->save();
        } catch (\Exception $e) {
            throw new CouldNotDeleteException('Could not delete coupon code');
        }
        if ($quote->getCouponCode() != '') {
            throw new CouldNotDeleteException('Could not delete coupon code');
        }
        return true;
    }
}
