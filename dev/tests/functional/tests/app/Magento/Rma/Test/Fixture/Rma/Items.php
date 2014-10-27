<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Fixture\Rma;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\FixtureFactory;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Source rma items.
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

        $this->data =  isset($data['presets']) ? $this->getPreset($data['presets']) : [];
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
        $presets = [];

        return isset($presets[$name]) ? $presets[$name] : [];
    }
}
