<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Coupon;

use \Magento\Checkout\Service\V1\Data\Cart\CouponBuilder as CouponBuilder;
use \Magento\Checkout\Service\V1\Data\Cart\Coupon as Coupon;

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
    public function get($cartId)
    {
        /** @var  \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        $data = [Coupon::COUPON_CODE => $quote->getCouponCode()];
        $output = $this->couponBuilder->populateWithArray($data)->create();
        return $output;
    }
}
