<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

use Mtf\Fixture\FixtureInterface;

/**
 * Class ConfigurableAttributesData
 * Source configurable attributes data of the configurable products
 */
class ConfigurableAttributesData implements FixtureInterface
{
    /**
     * Data set resource
     *
     * @var array
     */
    protected $data;

    /**
     * Source constructor
     *
     * @param array $params
     * @param array $dependenceData
     * @param array $data
     */
    public function __construct(array $params, array $dependenceData, array $data = [])
    {
        $this->data[$dependenceData['configurable_options']['attribute_id']] = [];

        $data = & $this->data[$dependenceData['configurable_options']['attribute_id']];
        $data['id'] = $dependenceData['configurable_options']['id'];
        $data['attribute_id'] = $dependenceData['configurable_options']['attribute_id'];
        $data['code'] = $dependenceData['configurable_options']['code'];
        $data['label'] = $dependenceData['configurable_options']['label'];

        foreach ($dependenceData['configurable_options']['values'] as $values) {
            $data['values'][$values['value_index']] = $values;
        }

        $this->params = $params;
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
     */
    public function getData($key = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $this->data;
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
