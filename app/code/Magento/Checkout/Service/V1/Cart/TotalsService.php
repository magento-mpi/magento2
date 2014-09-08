<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Cart;

use Magento\Checkout\Service\V1\Data\Cart;
use Magento\Sales\Model\Quote;
use Magento\Sales\Model\QuoteRepository;
use \Magento\Checkout\Service\V1\Data\Cart\Totals;

class TotalsService implements TotalsServiceInterface
{
    /**
     * @var Cart\TotalsBuilder
     */
    private $totalsBuilder;

    /**
     * @var Cart\TotalsMapper
     */
    private $totalsMapper;

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var Totals\ItemMapper;
     */
    private $itemTotalsMapper;

    /**
     * @param Cart\TotalsBuilder $totalsBuilder
     * @param Cart\TotalsMapper $totalsMapper
     * @param QuoteRepository $quoteRepository
     * @param Totals\ItemMapper $itemTotalsMapper
     */
    public function __construct(
        Cart\TotalsBuilder $totalsBuilder,
        Cart\TotalsMapper $totalsMapper,
        QuoteRepository $quoteRepository,
        Totals\ItemMapper $itemTotalsMapper
    ) {
        $this->totalsBuilder = $totalsBuilder;
        $this->totalsMapper = $totalsMapper;
        $this->quoteRepository = $quoteRepository;
        $this->itemTotalsMapper = $itemTotalsMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotals($cartId)
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);

        $this->totalsBuilder->populateWithArray($this->totalsMapper->map($quote));
        $items = [];
        foreach ($quote->getAllItems() as $item) {
            $items[] = $this->itemTotalsMapper->extractDto($item);
        }
        $this->totalsBuilder->setItems($items);

        return $this->totalsBuilder->create();
    }
}
