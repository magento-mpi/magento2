<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Fixture\CatalogRule;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\FixtureFactory;

/**
 * Class Conditions
 *
 * Data keys:
 *  - dataSet
 */
class Conditions implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var \Mtf\Fixture\FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param string $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, $data)
    {
        preg_match('/\[(.*)\]/', $data, $matches);
        $conditions_array = explode(",", $matches[1]);
        $value = array_shift($conditions_array);
        $parts = explode('|', $value);

        foreach ($parts as $key => $value) {
            $parts[$key] = trim($value);
        }

        if ($parts[0] == 'Category') {
            $this->data['conditions']['1--1']['attribute'] = 'category_ids';
        } elseif ($parts[1] == 'AttributeSet') {
            $this->data['conditions']['1--1']['attribute'] = 'attribute_set_id';
        }

        if ($parts[1] == 'is') {
            $this->data['conditions']['1--1']['operator'] = '==';
        } else {
            $this->data['conditions']['1--1']['operator'] = '!=';
        }

        $this->data['conditions']['1--1']['type'] = 'Magento\CatalogRule\Model\Rule\Condition\Product';

        if (!empty($parts[2])) {
            $this->data['conditions']['1--1']['value'] = $parts[2];
        }
    }

    /**
     * Persist custom selections conditions
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
     * @param $key [optional]
     * @return mixed
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
}
