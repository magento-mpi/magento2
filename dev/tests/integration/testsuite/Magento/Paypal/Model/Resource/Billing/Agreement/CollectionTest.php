<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Model\Resource\Billing\Agreement;

use Magento\TestFramework\Helper\Bootstrap;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Paypal/_files/billing_agreement.php
     */
    public function testAddCustomerDetails()
    {
        /** @var \Magento\Paypal\Model\Resource\Billing\Agreement\Collection $billingAgreementCollection */
        $billingAgreementCollection = Bootstrap::getObjectManager()
            ->create('Magento\Paypal\Model\Resource\Billing\Agreement\Collection');

        $billingAgreementCollection->addCustomerDetails();

        $this->assertEquals(1, $billingAgreementCollection->count(), "Invalid collection items quantity.");
        /** @var \Magento\Paypal\Model\Billing\Agreement $billingAgreement */
        $billingAgreement = $billingAgreementCollection->getFirstItem();

        $expectedData = [
            'customer_id' => 1,
            'method_code' => 'paypal_express',
            'reference_id' => 'REF-ID-TEST-678',
            'status' => 'active',
            'store_id' => 1,
            'agreement_label' => 'TEST',
            'customer_email' => 'customer@example.com',
            'customer_firstname' => 'Firstname',
            'customer_lastname' => 'Lastname'
        ];
        foreach ($expectedData as $field => $expectedValue) {
            $this->assertEquals(
                $expectedValue,
                $billingAgreement->getData($field),
                "'{$field}' field value is invalid."
            );
        }
    }
}
