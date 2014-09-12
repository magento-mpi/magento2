<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Fixture\OrderInjectable;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\Store\Test\Fixture\Store;

/**
 * Class StoreId
 * Prepare StoreId for Store Group
 */
class StoreId implements FixtureInterface
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
     * Store fixture
     *
     * @var Store
     */
    public $store;

    /**
     * Constructor
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
            /** @var Store $website */
            if (!$store->getStoreId()) {
                $store->persist();
            }
            $this->store = $store;
            $this->data = $store->getName();
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
     * Return Store fixture
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->store;
    }
}
