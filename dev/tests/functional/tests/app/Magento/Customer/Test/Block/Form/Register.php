<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Form;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;

/**
 * Class Register
 * Register new customer on Frontend
 *
 */
class Register extends Form
{
    /**
     * 'Submit' form button
     *
     * @var string
     */
    protected $submit = '.action.submit';

    /**
     * Locator for customer attribute on New Order page
     *
     * @var string
     */
    protected $customerAttribute = "[name='%s']";

    /**
     * Create new customer account and fill billing address if it exists
     *
     * @param FixtureInterface $fixture
     * @param $address
     */
    public function registerCustomer(FixtureInterface $fixture, $address = null)
    {
        $this->fill($fixture);
        if ($address !== null) {
            $this->fill($address);
        }
        $this->_rootElement->find($this->submit, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Check if Customer custom Attribute visible
     *
     * @param CustomerCustomAttribute $customerAttribute
     * @return bool
     */
    public function isCustomerAttributeVisible(CustomerCustomAttribute $customerAttribute)
    {
        return $this->_rootElement->find(
            sprintf($this->customerAttribute, $customerAttribute->getAttributeCode())
        )->isVisible();
    }
}
