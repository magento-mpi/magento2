<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Onepage;

use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Billing
 * One page checkout status billing block
 */
class Billing extends Form
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '#billing-buttons-container button';

    /**
     * 'Ship to different address' radio button
     *
     * @var string
     */
    protected $useForShipping = '[id="billing:use_for_shipping_no"]';

    /**
     * Wait element
     *
     * @var string
     */
    protected $waitElement = '.loading-mask';

    /**
     * Selector for customer attribute block by label
     *
     * @var string
     */
    protected $customerAttributeFieldByLabel = './/div[contains(@class,"field") and (./label/span[.="%s"])]';

    /**
     * Mapping selectors for fields of customer attributes
     *
     * @var array
     */
    protected $mapCustomerAttributeTypeFields = [
        'Text Field' => './*[@class="control"]/input[@type="text"]',
        'Text Area' => './*[@class="control"]/textarea',
        'Multiple Line' => './*[@class="control"]/input[@type="text"]',
        'Date' => './/*[@class="control" and (//div[contains(@class,"month")]) and (//div[contains(@class,"day")]) and (//div[contains(@class,"year")])]',
        'Dropdown' => './*[@class="control"]/select',
        'Multiple Select' => './*[@class="control"]/select[@multiple="multiple"]',
        'Yes/No' => './*[@class="control"]/select',
        'File (attachment)' => './*[@class="control"]/input[@type="file"]',
        'Image File' => './*[@class="control"]/input[@type="file"]',
    ];

    /**
     * Fill billing address
     *
     * @param Checkout $fixture
     * @return void
     */
    public function fillBilling(Checkout $fixture)
    {
        $billingAddress = $fixture->getBillingAddress();
        if ($billingAddress) {
            $this->fill($billingAddress);
        }
        if ($fixture->getShippingAddress()) {
            $this->_rootElement->find($this->useForShipping, Locator::SELECTOR_CSS)->click();
        }
        $this->clickContinue();
    }

    /**
     * Click continue on billing information block
     *
     * @return void
     */
    public function clickContinue()
    {
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible($this->waitElement);
    }

    /**
     * Check for visible customer attribute
     *
     * @param CustomerCustomAttribute $customerAttribute
     * @return bool
     * @throws \Exception
     */
    public function isCustomerAttributeVisible(CustomerCustomAttribute $customerAttribute)
    {
        $inputType = $customerAttribute->getFrontendInput();
        if (!isset($this->mapCustomerAttributeTypeFields[$inputType])) {
            throw new \Exception("Can't find \"{$inputType}\" field in mapping customer attribute fields.");
        }

        $customerAttributeElement = $this->getCustomerAttributeField($customerAttribute->getFrontendLabel());
        $inputField = $customerAttributeElement->find(
            $this->mapCustomerAttributeTypeFields[$inputType],
            Locator::SELECTOR_XPATH
        );

        return $customerAttributeElement->isVisible() && $inputField->isVisible();
    }

    /**
     * Get customer attribute field block by label
     *
     * @param string $label
     * @return Element
     */
    public function getCustomerAttributeField($label)
    {
        return $this->_rootElement->find(
            sprintf($this->customerAttributeFieldByLabel, $label),
            Locator::SELECTOR_XPATH
        );
    }
}
