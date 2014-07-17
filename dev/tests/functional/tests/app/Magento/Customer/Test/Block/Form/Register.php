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
     * @param string $attributeCode
     * @return bool
     */
    public function isCustomerAttributeVisible($attributeCode)
    {
        $selector = "[name='$attributeCode']";
        return $this->_rootElement->find($selector)->isVisible();
    }
}
