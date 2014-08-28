<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Fixture\OrderInjectable;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class BillingAddressId
 * Billing address  preset
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
        if (isset($data['preset'])) {
            $this->currentPreset = $data['preset'];
            $this->data = $this->getPreset($this->currentPreset);
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
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Preset array
     *
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                'firstname' => '%customer_first_name%',
                'lestname' => '%customer_last_name%',
                'street' => ['Улица Пушкина дом короля.'],
                'city' => 'Los Angeles',
                'country_id' => 'US',
                'region_id' => 'California',
                'postcode' => 90001,
                'telephone' => '0980074678',
            ]
        ];
        if (!isset($presets[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Wrong Order preset name: %s', $name)
            );
        }
        return $presets[$name];
    }
}
