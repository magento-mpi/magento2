<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Fixture\CmsBlock;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class StoreId
 *
 * Data keys:
 *  - dataSet
 */
class StoreId implements FixtureInterface
{
    /**
     * Array with store names
     *
     * @var array
     */
    protected $data = [];

    /**
     * Array with store fixture
     *
     * @var array
     */
    protected $stores;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Create Store if
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataSet'])) {
            $dataSets = $data['dataSet'];
            foreach ($dataSets as $dataSet) {
                if ($dataSet !== '-') {
                    /** @var \Magento\Store\Test\Fixture\Store $store */
                    $store = $fixtureFactory->createByCode('store', ['dataSet' => $dataSet]);
                    if (!$store->hasData('store_id')) {
                        $store->persist();
                    }
                    $this->stores[] = $store;
                    $this->data[] = $store->getName() == 'All Store Views' ? $store->getName() :
                        'Main Website' . '/' . $store->getGroupId() . '/' . $store->getName();
                }
            }
        }
    }

    /**
     * Persist custom selections store view
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
     * @return array
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
