<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Mtf\Client\Browser;
use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Page\SalesGuestView;

/**
 * Click on "Print Order" button.
 */
class PrintOrderOnFrontendStep implements TestStepInterface
{
    /**
     * View orders page.
     *
     * @var SalesGuestView
     */
    protected $salesGuestView;

    /**
     * Browser.
     *
     * @var Browser
     */
    protected $browser;

    /**
     * @constructor
     * @param SalesGuestView $salesGuestView
     * @param Browser $browser
     */
    public function __construct(SalesGuestView $salesGuestView, Browser $browser)
    {
        $this->salesGuestView = $salesGuestView;
        $this->browser = $browser;
    }

    /**
     * Click on "Print Order" button.
     *
     * @return void
     */
    public function run()
    {
        $this->salesGuestView->getViewBlock()->clickPrintOrder();
        $this->browser->selectWindow();
    }
}
