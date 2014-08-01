<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Fixture\GiftRegistry;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Address
 * Prepare Address for gift registry
 */
class Address implements FixtureInterface
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
     * @param array $params [optional]
     * @param array $data [optional]
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
        } else {
            $this->data = $data;
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
     * Preset for address gift registry
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'company' => 'Magento %isolation%',
                'street' => '6161 West Centinela Avenue',
                'city' => 'Culver City',
                'region_id' => 'California',
                'postcode' => '90230',
                'country_id' => 'United States',
                'telephone' => '555-55-555-55',
            ]
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
