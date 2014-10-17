<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CheckoutAgreements\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\CheckoutAgreements\Test\Fixture\CheckoutAgreement;
use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementNew;
use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementIndex;

/**
 * Test creation for UpdateTermEntityTest
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
 * 3. Fill data from dataSet
 * 4. Save
 * 5. Perform all assertions
 *
 * @group Terms_and_Conditions_(CS)
 * @ZephyrId MAGETWO-29635
 */
class UpdateTermEntityTest extends Injectable
{
    /**
     * Delete all terms on backend
     *
     * @return void
     */
    public function __prepare()
    {
        $this->objectManager->create('Magento\CheckoutAgreements\Test\TestStep\DeleteAllTermsEntityStep')->run();
    }

    /**
     * Set up configuration
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
     * Update Term Entity test
     *
     * @param CheckoutAgreementNew $agreementNew
     * @param CheckoutAgreementIndex $agreementIndex
     * @param CheckoutAgreement $agreement
     * @param CheckoutAgreement $agreementOrigin
     * @return void
     */
    public function test(
        CheckoutAgreementNew $agreementNew,
        CheckoutAgreementIndex $agreementIndex,
        CheckoutAgreement $agreement,
        CheckoutAgreement $agreementOrigin
    ) {
        // Precondition
        $agreementOrigin->persist();

        // Steps
        $agreementIndex->open();
        $agreementIndex->getAgreementGridBlock()->searchAndOpen(['name' => $agreementOrigin->getName()]);
        $agreementNew->getAgreementsForm()->fill($agreement);
        $agreementNew->getPageActionsBlock()->save();
    }

    /**
     * Disable enabled config after test and delete all terms on backend
     *
     * @return void
     */
    public function tearDown()
    {
        $setConfigStep = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'checkout_term_condition', 'rollback' => true]
        );
        $setConfigStep->run();

        $this->objectManager->create('Magento\CheckoutAgreements\Test\TestStep\DeleteAllTermsEntityStep')->run();
    }
}
