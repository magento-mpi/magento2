<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile quote model
 */
namespace Magento\RecurringProfile\Model;

class QuoteImporter
{
    /**
     * @var \Magento\RecurringProfile\Model\ProfileFactory
     */
    protected $_profileFactory;

    /**
     * @param \Magento\RecurringProfile\Model\ProfileFactory $profileFactory
     */
    public function __construct(\Magento\RecurringProfile\Model\ProfileFactory $profileFactory)
    {
        $this->_profileFactory = $profileFactory;
    }

    /**
     * Prepare recurring payment profiles
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @throws \Exception
     * @return array
     */
    public function prepareRecurringPaymentProfiles(\Magento\Sales\Model\Quote $quote)
    {
        if (!$quote->getTotalsCollectedFlag()) {
            throw new \Exception('Quote totals must be collected before this operation.');
        }

        $result = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            if (is_object($product) && ($product->getIsRecurring() == '1')
                && $profile = $this->_profileFactory->create()->importProduct($product)
            ) {
                $profile->importQuote($quote);
                $profile->importQuoteItem($item);
                $result[] = $profile;
            }
        }
        return $result;
    }
}
