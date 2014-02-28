<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Block\Plugin;

use Magento\Checkout\Model\Session;
use Magento\RecurringProfile\Model\Quote\Filter;

class Payment
{
    /** @var Filter  */
    protected $filter;

    /** @var  Session */
    protected $session;

    /**
     * @param Session $session
     * @param Filter $filter
     */
    public function __construct(
        Session $session,
        Filter $filter
    ) {
        $this->session = $session;
        $this->filter = $filter;
    }

    /**
     * Add hasRecurringItems option
     *
     * @param \Magento\Checkout\Block\Onepage\Payment $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetOptions(\Magento\Checkout\Block\Onepage\Payment $subject, array $result)
    {
        $quote = $this->session->getQuote();
        $result['hasRecurringItems'] = $quote && $this->filter->hasRecurringItems($quote);
        return $result;
    }
} 
