<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;

/**
 * Class DisplayState
 * Create data for fixture
 */
class DisplayState implements FixtureInterface
{
    /**
     * Display states values
     *
     * @var array
     */
    protected $data;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        array $params,
        array $data = []
    ) {
        $this->params = $params;
        foreach ($data as $key => $value) {
            if ($value !== '-') {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * Persist
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data
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
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }
}
