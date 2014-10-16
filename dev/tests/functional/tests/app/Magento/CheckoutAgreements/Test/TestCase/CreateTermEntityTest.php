<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CheckoutAgreements\Test\TestCase;

use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementIndex;
use Magento\CheckoutAgreements\Test\Page\Adminhtml\CheckoutAgreementNew;
use Magento\CheckoutAgreements\Test\Fixture\CheckoutAgreement;
use Mtf\TestCase\Injectable;
use Mtf\ObjectManager;

/**
 * Test creation for CreateTermEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable "Terms and Conditions": Stores > Configuration > Sales > Checkout > Checkout Options
 *
 * Steps:
 * 1. Open Backend Stores> Terms and Conditions
 * 2. Create new "Terms and Conditions"
 * 3. Fill data from dataSet
 * 4. Save
 * 5. Perform all assertions
 *
 * @group Terms and Conditions (CS)
 * @ZephyrId MAGETWO-29586
 */
class CreateTermEntityTest extends Injectable
{
    /**
     * Set up configuration and delete all terms on backend
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function __inject(ObjectManager $objectManager)
    {
        $setConfigStep = $objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'checkout_term_condition']
        );
        $setConfigStep->run();

        $deleteTerms = $this->objectManager
            ->create('Magento\CheckoutAgreements\Test\TestStep\DeleteAllTermsEntityStep');
        $deleteTerms->run();
    }

    /**
     * Create Term Entity test
     *
     * @param CheckoutAgreementNew $agreementNew
     * @param CheckoutAgreementIndex $agreementIndex
     * @param CheckoutAgreement $conditions
     * @return void
     */
    public function test(
        CheckoutAgreementNew $agreementNew,
        CheckoutAgreementIndex $agreementIndex,
        CheckoutAgreement $conditions
    ) {
        // Steps
        $agreementIndex->open();
        $agreementIndex->getPageActionsBlock()->addNew();
        $agreementNew->getAgreementsForm()->fill($conditions);
        $agreementNew->getPageActionsBlock()->save();
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
            ['configData' => 'checkout_term_condition', 'rollback' => true]
        );
        $setConfigStep->run();
    }
}
