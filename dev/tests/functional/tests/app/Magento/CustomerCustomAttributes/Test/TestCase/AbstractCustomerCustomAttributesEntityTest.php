<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Magento\CustomerCustomAttributes\Test\Page\Adminhtml\CustomerAttributeNew;
use Magento\CustomerCustomAttributes\Test\Page\Adminhtml\CustomerAttributeIndex;

/**
 * Class AbstractCustomerCustomAttributesEntityTest
 * Parent class for CustomerCustomAttributes tests
 */
abstract class AbstractCustomerCustomAttributesEntityTest extends Injectable
{
    /**
     * Backend page with the list of customer attributes
     *
     * @var CustomerAttributeIndex
     */
    protected $customerAttributeIndex;

    /**
     * Backend page with new customer attribute form
     *
     * @var CustomerAttributeNew
     */
    protected $customerAttributeNew;

    /**
     * Fixture CustomerCustomAttribute
     *
     * @var CustomerCustomAttribute
     */
    protected $customerCustomAttributes;

    /**
     * Preparing customer
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $customer->persist();

        return ['customer' => $customer];
    }

    /**
     * Injection data
     *
     * @param CustomerAttributeIndex $customerAttributeIndex
     * @param CustomerAttributeNew $customerAttributeNew
     * @return void
     */
    public function __inject(
        CustomerAttributeIndex $customerAttributeIndex,
        CustomerAttributeNew $customerAttributeNew
    ) {
        $this->customerAttributeIndex = $customerAttributeIndex;
        $this->customerAttributeNew = $customerAttributeNew;
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerCustomAttributes = (is_array($this->customerCustomAttributes))
            ? $this->customerCustomAttributes
            : [$this->customerCustomAttributes];
        foreach ($this->customerCustomAttributes as $customerCustomAttribute) {
            $filter = ['frontend_label' => $customerCustomAttribute->getFrontendLabel()];
            $this->customerAttributeIndex->open();
            $this->customerAttributeIndex->getCustomerCustomAttributesGrid()->searchAndOpen($filter);
            $this->customerAttributeNew->getFormPageActions()->delete();
        }
    }
}
