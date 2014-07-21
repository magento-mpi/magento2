<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1;

use \Magento\Framework\Exception\NoSuchEntityException;

class QuoteLoader
{
    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $quoteFactory;


    /**
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     */
    public function __construct(\Magento\Sales\Model\QuoteFactory $quoteFactory)
    {
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @param int $cartId
     * @return \Magento\Sales\Model\Quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function load($cartId)
    {
        $quote = $this->quoteFactory->create();
        $quote->load($cartId);
        if (!$quote->getId()) {
            throw NoSuchEntityException::singleField('cartId', $cartId);
        }
        return $quote;
    }
}
