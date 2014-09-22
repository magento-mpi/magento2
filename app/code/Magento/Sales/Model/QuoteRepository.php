<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model;

use \Magento\Framework\Exception\NoSuchEntityException;

class QuoteRepository
{
    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param QuoteFactory $quoteFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Get quote by id
     *
     * @param int $cartId
     * @return Quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($cartId)
    {
        return $this->loadQuote('load', 'cartId', $cartId);
    }


    /**
     * Get quote by customer Id
     *
     * @param int $customerId
     * @return Quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getForCustomer($customerId)
    {
        return $this->loadQuote('loadByCustomer', 'customerId', $customerId);
    }

    /**
     * Load quote with different methods
     *
     * @param string $loadMethod
     * @param string $loadField
     * @param int $identifier
     * @return Quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function loadQuote($loadMethod, $loadField, $identifier)
    {
        $quote = $this->quoteFactory->create();
        $quote->setStoreId($this->storeManager->getStore()->getId())->$loadMethod($identifier);
        if (!$quote->getId() || !$quote->getIsActive()) {
            throw NoSuchEntityException::singleField($loadField, $identifier);
        }
        return $quote;
    }
}
