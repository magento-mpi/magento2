<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Reports\Test\Page\Adminhtml\Bestsellers;

/**
 * Test Flow:
 * Preconditions:
 * 1. Create customer.
 * 2. Create product.
 * 3. Place order.
 * 4. Refresh statistic.
 *
 * Steps:
 * 1. Open Backend.
 * 2. Go to Reports > Products > Bestsellers.
 * 3. Select time range, report period.
 * 4. Click "Show report".
 * 5. Perform all assertions.
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-28222
 */
class BestsellerProductsReportEntityTest extends Injectable
{
    /**
     * Bestsellers page.
     *
     * @var Bestsellers
     */
    protected $bestsellers;

    /**
     * Inject pages.
     *
     * @param Bestsellers $bestsellers
     * @return void
     */
    public function __inject(Bestsellers $bestsellers)
    {
        $this->bestsellers = $bestsellers;
        ObjectManager::getInstance()->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => "checkmo"]
        )->run();
    }

    /**
     * Bestseller Products Report.
     *
     * @param OrderInjectable $order
     * @param array $bestsellerReport
     * @return void
     */
    public function test(OrderInjectable $order, array $bestsellerReport)
    {
        // Preconditions
        $order->persist();
        $this->bestsellers->open();
        $this->bestsellers->getMessagesBlock()->clickLinkInMessages('notice', 'here');

        // Steps
        $this->bestsellers->getFilterBlock()->viewsReport($bestsellerReport);
        $this->bestsellers->getActionsBlock()->showReport();
    }
}
