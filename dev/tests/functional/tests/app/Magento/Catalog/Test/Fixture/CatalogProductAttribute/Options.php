<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture\CatalogProductAttribute;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Options
 * Prepare Manage Options for attribute
 */
class Options implements FixtureInterface
{
    /**
     * @constructor
     * @param array $params
     * @param array $data
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
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
     * @param string|null $key
     * @return mixed
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
     * Preset for Attribute manage options
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                [
                    'is_default' => 'Yes',
                    'admin' => 'blue',
                    'view' => '',
                ]
            ],
        ];
        if (!isset($presets[$name])) {
            return null;
        }
        return $presets[$name];
    }
}
