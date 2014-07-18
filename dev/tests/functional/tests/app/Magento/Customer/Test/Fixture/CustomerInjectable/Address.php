<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Fixture\CustomerInjectable;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlTransport;
use Magento\Customer\Test\Fixture\AddressInjectable;

/**
 * Class Address
 *
 * Data keys:
 *  - dataSet
 *  - address
 */
class Address implements FixtureInterface
{
    /**
     * Customer address
     *
     * @var AddressInjectable
     */
    protected $address;

    /**
     * Customer name
     *
     * @var string
     */
    protected $data;

    /**
     * @construct
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataSet']) && $data['dataSet'] !== '-') {
            $this->address = $fixtureFactory->createByCode('addressInjectable', ['dataSet' => $data['dataSet']]);
            $this->data = $this->address->getFirstname();
        }
        if (isset($data['address']) && $data['address'] instanceof AddressInjectable) {
            $this->address = $data['address'];
            $this->data = $data['address']->getFirstname();
        }
    }

    /**
     * Persist customer address
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
     * @param string $key [optional]
     * @return string|null
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
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return customer address
     *
     * @return AddressInjectable
     */
    public function getAddress()
    {
        return $this->address;
    }
}
