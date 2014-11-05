<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Fixture\OrderInjectable;

use Magento\Customer\Test\Fixture\AddressInjectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class BillingAddressId
 * Billing address data
 */
class BillingAddressId implements FixtureInterface
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
     * Current preset
     *
     * @var string
     */
    protected $currentPreset;

    /**
     * Constructor
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $data, array $params = [])
    {
        $this->params = $params;
        if (isset($data['value'])) {
            $this->data = $data['value'];
            return;
        }
        if (isset($data['dataSet'])) {
            $addresses = $fixtureFactory->createByCode('addressInjectable', ['dataSet' => $data['dataSet']]);
            $this->data = $addresses->getData();
            $this->data['street'] = [$this->data['street']];
        }
        if (isset($data['billingAddress']) && $data['billingAddress'] instanceof AddressInjectable) {
            /** @var AddressInjectable $address */
            $address = $data['billingAddress'];
            $this->data = $address->getData();
            $this->data['street'] = [$this->data['street']];
        }
    }

    /**
     * Persist order products
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
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }
}
