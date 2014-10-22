<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CheckoutAgreements\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\ObjectManager;
use Magento\CheckoutAgreements\Test\Fixture\CheckoutAgreement;
use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementNew;
use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementIndex;

/**
 * Test creation for DeleteTermEntityTest
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable "Terms and Conditions": Stores > Configuration > Sales > Checkout > Checkout Options
 * 2. Create term according to dataSet
 *
 * Steps:
 * 1. Open Backend Stores > Terms and Conditions
 * 2. Open created Term from preconditions
 * 3. Click on 'Delete' button
 * 4. Perform all assertions
 *
 * @group Terms_and_Conditions_(CS)
 * @ZephyrId MAGETWO-29687
 */
class DeleteTermEntityTest extends Injectable
{
    /**
     * Checkout agreement index page
     *
     * @var CheckoutAgreementIndex
     */
    protected $agreementIndex;

    /**
     * Checkout agreement new page
     *
     * @var CheckoutAgreementNew
     */
    protected $agreementNew;

    /**
     * Set up configuration
     *
     * @return void
     */
    public function __prepare()
    {
        $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'checkout_term_condition']
        )->run();
    }

    /**
     * Inject pages
     *
     * @param CheckoutAgreementNew $agreementNew
     * @param CheckoutAgreementIndex $agreementIndex
     * @return void
     */
    public function __inject(
        CheckoutAgreementNew $agreementNew,
        CheckoutAgreementIndex $agreementIndex
    ) {
        $this->agreementNew = $agreementNew;
        $this->agreementIndex = $agreementIndex;
    }

    /**
     * Delete Term Entity test
     *
     * @param CheckoutAgreement $agreement
     * @return void
     */
    public function test(CheckoutAgreement $agreement)
    {
        // Precondition
        $agreement->persist();

        // Steps
        $this->agreementIndex->open()->getAgreementGridBlock()->searchAndOpen(['name' => $agreement->getName()]);
        $this->agreementNew->getPageActionsBlock()->delete();
    }

    /**
     * Set default configuration
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        ObjectManager::getInstance()->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'checkout_term_condition', 'rollback' => true]
        )->run();
    }
}
