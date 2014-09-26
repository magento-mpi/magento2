<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Fixture\Reward;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CustomerId
 * Prepare data for customer_id field in customer balance fixture
 *
 * Data keys:
 *  - dataSet
 *  - customer
 */
class CustomerId implements FixtureInterface
{
    /**
     * Customer email
     *
     * @var string
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['customer']) && $data['customer'] instanceof CustomerInjectable) {
            $this->customer = $data['customer'];
            $this->data = $this->customer->getEmail();
        }
        if (isset($data['dataSet'])) {
            /** @var CustomerInjectable $customer */
            $customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => $data['dataSet']]);
            if (!$customer->hasData('id')) {
                $customer->persist();
            }
            $this->customer = $customer;
            $this->data = $customer->getEmail();
        }
    }

    /**
     * Persists prepared data into application
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param string|null $key
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return customer fixture
     *
     * @return CustomerInjectable
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
