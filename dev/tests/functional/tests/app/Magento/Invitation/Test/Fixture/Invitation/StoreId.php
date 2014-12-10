<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Invitation\Test\Fixture\Invitation;

use Magento\Store\Test\Fixture\Store;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Prepare StoreId.
 */
class StoreId implements FixtureInterface
{
    /**
     * Prepared dataSet data.
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings.
     *
     * @var array
     */
    protected $params;

    /**
     * Store fixture.
     *
     * @var Store
     */
    public $store;

    /**
     * Source constructor.
     *
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $data, array $params = [])
    {
        $this->params = $params;
        if (isset($data['dataSet'])) {
            $store = $fixtureFactory->createByCode('store', ['dataSet' => $data['dataSet']]);
            if (!$store->getStoreId()) {
                $store->persist();
            }
            $this->store = $store;
            $this->data = $store->getGroupId() . '/' . $store->getName();
        }
    }

    /**
     * Persist attribute options.
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
     * Return data set configuration settings.
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return Store fixture.
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->store;
    }
}
