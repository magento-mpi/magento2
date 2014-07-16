<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Fixture\UrlRewrite;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\Store\Test\Fixture\Store;

/**
 * Class StoreId
 * Store id source
 */
class StoreId implements FixtureInterface
{
    /**
     * Resource data
     *
     * @var string
     */
    protected $data;

    /**
     * Data set configuration settings
     *
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
        $this->params = $params;
        if (preg_match('`%(.*?)%`', $data, $store)) {
            /** @var Store $storeFixture */
            $storeFixture = $fixtureFactory->createByCode('store', ['dataSet' => $store[1]]);
            $storeFixture->persist();
            $data = str_replace('%' . $store[1] . '%', $storeFixture->getName(), $data);
        }
        $this->data = $data;
    }

    /**
     * Persist custom selections products
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
     * @param string|null $key [optional]
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
}
