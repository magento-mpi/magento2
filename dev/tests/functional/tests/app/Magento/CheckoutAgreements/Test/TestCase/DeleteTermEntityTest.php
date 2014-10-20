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
 * 3. Delete Term
 * 4. Perform all assertions
 *
 * @group Terms_and_Conditions_(CS)
 * @ZephyrId MAGETWO-29687
 */
class DeleteTermEntityTest extends Injectable
{
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
     * Update Term Entity test
     *
     * @param CheckoutAgreementNew $agreementNew
     * @param CheckoutAgreementIndex $agreementIndex
     * @param CheckoutAgreement $agreementOrigin
     * @return void
     */
    public function test(
        CheckoutAgreementNew $agreementNew,
        CheckoutAgreementIndex $agreementIndex,
        CheckoutAgreement $agreementOrigin
    ) {
        // Precondition
        $agreementOrigin->persist();

        // Steps
        $agreementIndex->open();
        $agreementIndex->getAgreementGridBlock()->searchAndOpen(['name' => $agreementOrigin->getName()]);
        $agreementNew->getPageActionsBlock()->delete();
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
