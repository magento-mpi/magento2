<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase;

use Mtf\TestCase\Scenario;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Reward\Test\Page\Adminhtml\RewardRateNew;
use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;

/**
 * OnePageCheckout within offline Payment Methods
 *
 * Test Flow:
 * Preconditions:
 * 1. Configure shipping method.
 * 2. Configure payment method.
 * 3. Create products.
 * 4. Create and setup customer.
 * 5. Create gift card account according to dataset.
 * 6. Create sales rule according to dataset.
 *
 * Steps:
 * 1. Go to Frontend.
 * 2. Add products to the cart.
 * 3. Apply discounts in shopping cart according to dataset.
 * 4. Click the 'Proceed to Checkout' button.
 * 5. Select checkout method according to dataset.
 * 6. Fill billing information and select the 'Ship to this address' option.
 * 7. Select shipping method.
 * 8. Select payment method (use reward points and store credit if available).
 * 9. Verify order total on review step.
 * 10. Place order.
 * 11. Perform assertions.
 *
 * @group One_Page_Checkout_(CS)
 * @ZephyrId MAGETWO-27485
 */
class OnePageCheckoutTest extends Scenario
{
    /**
     * Customer logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Reward rate index page
     *
     * @var RewardRateIndex
     */
    protected $rewardRateIndexPage;

    /**
     * Reward rate new page
     *
     * @var RewardRateNew
     */
    protected $rewardRateNewPage;

    /**
     * Preparing configuration for test
     *
     * @param CustomerAccountLogout $customerAccountLogout
     * @param RewardRateIndex $rewardRateIndexPage
     * @param RewardRateNew $rewardRateNewPage
     * @return void
     */
    public function __prepare(
        CustomerAccountLogout $customerAccountLogout,
        RewardRateIndex $rewardRateIndexPage,
        RewardRateNew $rewardRateNewPage
    ) {
        $this->customerAccountLogout = $customerAccountLogout;
        $this->rewardRateIndexPage = $rewardRateIndexPage;
        $this->rewardRateNewPage = $rewardRateNewPage;
    }

    /**
     * Runs one page checkout test
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario();
    }

    /**
     * Disable enabled config after test
     *
     * @return void
     */
    public function tearDown()
    {
        $setConfigStep = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->currentVariation['arguments']['configData'], 'rollback' => true]
        );
        $setConfigStep->run();
        $this->customerAccountLogout->open();
        // Deleting exchange rates
        $this->rewardRateIndexPage->open();
        while ($this->rewardRateIndexPage->getRewardRateGrid()->isFirstRowVisible()) {
            $this->rewardRateIndexPage->getRewardRateGrid()->openFirstRow();
            $this->rewardRateNewPage->getFormPageActions()->delete();
        }
    }
}
