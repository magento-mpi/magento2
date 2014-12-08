<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CheckoutAgreements\Test\TestCase;

use Magento\CheckoutAgreements\Test\Fixture\CheckoutAgreement;
use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementIndex;
use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementNew;
use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;

/**
 * Test creation for CreateTermEntity.
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable "Terms and Conditions": Stores > Configuration > Sales > Checkout > Checkout Options
 *
 * Steps:
 * 1. Open Backend Stores > Terms and Conditions
 * 2. Create new "Terms and Conditions"
 * 3. Fill data from dataSet
 * 4. Save
 * 5. Perform all assertions
 *
 * @group Terms_and_Conditions_(CS)
 * @ZephyrId MAGETWO-29586
 */
class CreateTermEntityTest extends Injectable
{
    // TODO: Move set up configuration to "__prepare" method after fix bug MAGETWO-29331
    /**
     * Set up configuration.
     *
     * @return void
     */
    public function __inject()
    {
        $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'checkout_term_condition']
        )->run();
    }

    /**
     * Create Term Entity test.
     *
     * @param CheckoutAgreementNew $agreementNew
     * @param CheckoutAgreementIndex $agreementIndex
     * @param CheckoutAgreement $agreement
     * @return void
     */
    public function test(
        CheckoutAgreementNew $agreementNew,
        CheckoutAgreementIndex $agreementIndex,
        CheckoutAgreement $agreement
    ) {
        // Steps
        $agreementIndex->open();
        $agreementIndex->getPageActionsBlock()->addNew();
        $agreementNew->getAgreementsForm()->fill($agreement);
        $agreementNew->getPageActionsBlock()->save();
    }

    /**
     * Clear data after test.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->objectManager->create('Magento\CheckoutAgreements\Test\TestStep\DeleteAllTermsEntityStep')->run();

        // TODO: Move set default configuration to "tearDownAfterClass" method after fix bug MAGETWO-29331
        ObjectManager::getInstance()->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'checkout_term_condition', 'rollback' => true]
        )->run();
    }
}
