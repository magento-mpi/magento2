<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Fixture\Store;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\Store\Test\Fixture\Store;

/**
 * Class StoreIds
 * Prepare Store
 */
class StoreIds implements FixtureInterface
{
    /**
     * Resource data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Return Cms page
     *
     * @var Store
     */
    protected $store = [];

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if ($data['dataSet'] && $data['dataSet'] != "-") {
            $dataSet = explode(',', $data['dataSet']);
            foreach ($dataSet as $store) {
                /** @var Store $store */
                $store = $fixtureFactory->createByCode('store', ['dataSet' => $store]);
                if (!$store->hasData('store_id')) {
                    $store->persist();
                }
                $this->cmsPage[] = $store;
                $this->data[] = $store->getStoreId();
            }
        } else {
            $this->data[] = null;
        }
    }

    /**
     * Persist Store
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
     * @param string|null $key
     * @return string
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
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->store;
    }
}
