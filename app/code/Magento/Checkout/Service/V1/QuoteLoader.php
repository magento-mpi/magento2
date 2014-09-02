<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1;

use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Authorization\Model\UserContextInterface;

class QuoteLoader
{
    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param UserContextInterface $userContext
     */
    public function __construct(
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        UserContextInterface $userContext
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->userContext = $userContext;
    }

    /**
     * Load quote
     *
     * @param int $cartId
     * @param int $storeId
     * @return \Magento\Sales\Model\Quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function load($cartId, $storeId)
    {
        $quote = $this->quoteFactory->create();
        $quote->setStoreId($storeId);
        $quote->load($cartId);
        if (!$quote->getId() || !$quote->getIsActive() || !$this->isAllowed($quote)) {
            throw NoSuchEntityException::singleField('cartId', $cartId);
        }
        return $quote;
    }

    /**
     * Check whether quote is allowed for current user context
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    protected function isAllowed(\Magento\Sales\Model\Quote $quote)
    {
        return $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER
            ? $quote->getCustomerId() == $this->userContext->getUserId()
            : true;
    }
}
