<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\TestCase\OnePageCheckoutTest\Test;

use Magento\Reward\Test\Page\Adminhtml\ExchangeRateIndex;
use Magento\Reward\Test\Page\Adminhtml\ExchangeRateNew;
use Mtf\TestCase\Step\StepInterface;

class DeleteRewardExchangeRate implements StepInterface
{
    /**
     * Reward Exchange Rate grid page
     *
     * @var ExchangeRateIndex
     */
    protected $exchangeRateIndexPage;

    /**
     * Reward Exchange Rate edit page
     *
     * @var ExchangeRateNew
     */
    protected $exchangeRateNewPage;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param ExchangeRateIndex $exchangeRateIndex
     * @param ExchangeRateNew $exchangeRateNew
     */
    public function __construct(ExchangeRateIndex $exchangeRateIndex, ExchangeRateNew $exchangeRateNew)
    {
        $this->exchangeRateIndexPage = $exchangeRateIndex;
        $this->exchangeRateNewPage = $exchangeRateNew;
    }

    /**
     * Run step that delete reward exchange rate
     *
     * @return void
     */
    public function run()
    {
        $this->exchangeRateIndexPage->open();
        while($this->exchangeRateIndexPage->getExchangeRateGrid()->getFirstRow()->isVisible()) {
            $this->exchangeRateIndexPage->getExchangeRateGrid()->getFirstRow()->click();
            $this->exchangeRateNewPage->getFormPageActions()->delete();
            $this->exchangeRateIndexPage->getExchangeRateGrid()->reinitRootElement();
        }
    }
}
