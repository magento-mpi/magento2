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
     * Fill the form.
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        if ($fixture instanceof InjectableFixture) {
            /** @var OrderInjectable $fixture */
            /** @var CustomerInjectable $customer */
            $customer = $fixture->getDataFieldConfig('customer_id')['source']->getCustomer();

            $data = [
                'order_id' => $fixture->getId(),
                'billing_last_name' => $customer->getLastname(),
                'find_order_by' => 'Email Address',
                'email_address' => $customer->getEmail(),
            ];
        } else {
            $data = $fixture->getData();
        }

        $fields = isset($data['fields']) ? $data['fields'] : $data;
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping, $element);

        return $this;
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
