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
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var CouponBuilder
     */
    protected $couponBuilder;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param CouponBuilder $couponBuilder
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        CouponBuilder $couponBuilder,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->couponBuilder = $couponBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, \Magento\Checkout\Service\V1\Data\Cart\Coupon $couponCodeData)
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);
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
        $storeId = $this->storeManager->getStore()->getId();
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $storeId);
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
