<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class CustomerId
 * Prepare CustomerId for multiple wish list
 */
class CustomerId implements FixtureInterface
{
    /**
     * Prepared dataSet data
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Constructor
     *
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['customer']) && $data['customer'] instanceof CustomerInjectable) {
            $this->data = $data['customer'];
            return;
        }
        if (isset($data['preset'])) {
            $customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => $data['preset']]);
            if ($customer->hasData('id') === false) {
                $customer->persist();
            }
            $this->data = $customer;
        }
    }

    /**
     * Persist attribute options
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
     * @param string|null $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return null;
    }

    /**
     * Get customer fixture
     *
     * @return array
     */
    public function getCustomerId()
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }
}
