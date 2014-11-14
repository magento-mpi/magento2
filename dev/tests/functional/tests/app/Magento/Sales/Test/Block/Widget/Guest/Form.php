<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Widget\Guest;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\InjectableFixture;
use Mtf\Fixture\FixtureInterface;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Orders and Returns form search block.
 */
class Form extends \Mtf\Block\Form
{
    /**
     * Search button selector.
     *
     * @var string
     */
    protected $searchButtonSelector = '.action.submit';

    /**
     * Selector for loads form.
     *
     * @var string
     */
    protected $loadsForm = 'div[id*=oar] input';

    /**
     * Fill the form.
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @param bool $isSearchByEmail [optional]
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null, $isSearchByEmail = true)
    {
        if ($fixture instanceof InjectableFixture) {
            /** @var OrderInjectable $fixture */
            /** @var CustomerInjectable $customer */
            $customer = $fixture->getDataFieldConfig('customer_id')['source']->getCustomer();
            $data = [
                'order_id' => $fixture->getId(),
                'billing_last_name' => $customer->getLastname(),
            ];

            if ($isSearchByEmail) {
                $data['find_order_by'] = 'Email Address';
                $data['email_address'] = $customer->getEmail();
            } else {
                $data['find_order_by'] = 'ZIP Code';
                $data['billing_zip_code'] = $fixture->getDataFieldConfig('billing_address_id')['source']->getPostcode();
            }
        } else {
            $data = $fixture->getData();
        }
        $fields = isset($data['fields']) ? $data['fields'] : $data;
        $mapping = $this->dataMapping($fields);

        $this->waitLoadForm();
        $this->_fill($mapping, $element);

        return $this;
    }

    /**
     * Wait while form is loading
     *
     * @return void
     */
    protected function waitLoadForm()
    {
        $browser = $this->browser;
        $selector = $this->loadsForm;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $inputs = $browser->find($selector)->getElements();
                $i = 0;
                foreach ($inputs as $input) {
                    if ($input->isVisible()) {
                        ++$i;
                    }
                }
                return $i == 1 ? true : null;
            }
        );
    }

    /**
     * Submit search form.
     *
     * @return void
     */
    public function submit()
    {
        $this->_rootElement->find($this->searchButtonSelector, Locator::SELECTOR_CSS)->click();
    }
}
