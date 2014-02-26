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
     * @param array $result
     * @return array
     */
    public function afterGetOptions(array $result)
    {
        $quote = $this->session->getQuote();
        $result['hasRecurringItems'] = $quote && $this->filter->hasRecurringItems($quote);
        return $result;
    }
} 