<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture\CatalogCategory;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogCategory;

/**
 * Class CategoryProducts
 * Prepare products
 */
class CategoryProducts implements FixtureInterface
{
    /**
     * Return products
     *
     * @var array
     */
    protected $products = [];

    /**
     * Fixture params
     *
     * @var array
     */
    protected $params;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array|int $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, $data = [])
    {
        $this->params = $params;
        if ($data['dataSet'] == '-') {
            return;
        } else if ($data['dataSet']) {
            $dataSet = explode(',', $data['dataSet']);
            foreach ($dataSet as $value) {
                $explodeValue = explode('::', $value);
                $product = $fixtureFactory->createByCode($explodeValue[0], ['dataSet' => $explodeValue[1]]);
                $product->persist();
                $this->data[] = $product->getName();
                $this->products[] = $product;
            }
        } else {
            $this->data = $data;
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

    /**
     * Return products
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }
}
