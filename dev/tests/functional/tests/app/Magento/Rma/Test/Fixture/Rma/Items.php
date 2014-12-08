<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Fixture\Rma;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Source rma items.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Items implements FixtureInterface
{
    /**
     * Data set configuration settings.
     *
     * @var array
     */
    protected $params;

    /**
     * Prepared dataSet data.
     *
     * @var integer
     */
    protected $data = null;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;

        $this->data = isset($data['presets']) ? $this->getPreset($data['presets']) : [];
        if (isset($data['data'])) {
            $this->data = array_replace_recursive($this->data, $data['data']);
        }
    }

    /**
     * Persist custom selections products.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set.
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
     * Return data set configuration settings.
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return array preset.
     *
     * @param string $name
     * @return array
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                [
                    'qty_requested' => 1,
                    'reason' => 'Wrong size',
                    'condition' => 'Damaged',
                    'resolution' => 'Exchange',
                ],
            ],
        ];

        return isset($presets[$name]) ? $presets[$name] : [];
    }
}
