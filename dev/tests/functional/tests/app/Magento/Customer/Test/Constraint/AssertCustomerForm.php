<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;

/**
 * Class AssertCustomerForm
 *
 * @package Magento\Customer\Test\Constraint
 */
class AssertCustomerForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Skipped fields for verify data
     *
     * @var array
     */
    protected $skippedFields = [
        'password',
        'password_confirmation',
        'is_subscribed',
    ];

    /**
     * Assert that displayed customer data on edit page(backend) equals passed from fixture
     *
     * @param CustomerInjectable $customer
     * @param CustomerIndex $pageCustomerIndex
     * @param CustomerIndexEdit $pageCustomerIndexEdit
     * @param AddressInjectable $address [optional]
     * @return void
     */
    public function processAssert(
        CustomerInjectable $customer,
        CustomerIndex $pageCustomerIndex,
        CustomerIndexEdit $pageCustomerIndexEdit,
        AddressInjectable $address = null
    ) {
        $data = ['customer' => $customer->getData()];
        $filter = ['email' => $customer->getEmail()];

        if ($address) {
            $data['addresses'][1] = $address->hasData() ? $address->getData() : [];
        }

        $pageCustomerIndex->open();
        $pageCustomerIndex->getCustomerGridBlock()->searchAndOpen($filter);
        $dataForm = $pageCustomerIndexEdit->getCustomerForm()->getDataCustomer($customer, $address);
        \PHPUnit_Framework_Assert::assertTrue(
            $this->verifyData($data, $dataForm),
            'Customer data on edit page(backend) not equals to passed from fixture.'
        );
    }

    /**
     * Verify data in form equals to passed from fixture
     *
     * @param array $dataFixture
     * @param array $dataForm
     * @return bool
     */
    protected function verifyData(array $dataFixture, array $dataForm)
    {
        foreach($dataFixture as $key => $value) {
            if (in_array($key, $this->skippedFields)){
                continue;
            }
            if (!array_key_exists($key,$dataForm)) {
                return false;
            }

            if (
                is_array($value) && $this->verifyData($value, $dataForm[$key])
                || $value == $dataForm[$key]
            ) {
                continue;
            }
            return false;
        }
        return true;
    }

    /**
     * Text success verify Customer form
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed customer data on edit page(backend) equals to passed from fixture.';
    }
}
