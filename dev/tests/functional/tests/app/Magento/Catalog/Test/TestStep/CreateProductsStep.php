<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestStep;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Mtf\TestStep\TestStepInterface;

/**
 * Class CreateProductsStep
 * Create products using handler
 */
class CreateProductsStep implements TestStepInterface
{
    /**
     * Products names in data set
     *
     * @var string
     */
    protected $products;

    /**
     * Factory for Fixtures
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param string $products
     */
    public function __construct(FixtureFactory $fixtureFactory, $products)
    {
        $this->products = $products;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Create products
     *
     * @return array
     */
    public function run()
    {
        $products = [];
        $productsDataSets = explode(',', $this->products);
        foreach ($productsDataSets as $key => $productDataSet) {
            list($fixtureClass, $dataSet) = explode('::', $productDataSet);
            /** @var FixtureInterface[] $products */
            $products[$key] = $this->fixtureFactory->createByCode(
                trim($fixtureClass),
                ['dataSet' => trim($dataSet)]
            );
            $products[$key]->persist();
        }

        return ['products' => $products];
    }
}
