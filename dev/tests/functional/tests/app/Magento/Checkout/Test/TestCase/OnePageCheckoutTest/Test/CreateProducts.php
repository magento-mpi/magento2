<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase\OnePageCheckoutTest\Test;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Mtf\TestCase\Step\StepInterface;

/**
 * Class CreateProducts
 * Create products using handler
 */
class CreateProducts implements StepInterface
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
     * Run step that creates products
     *
     * @return array
     */
    public function run()
    {
        $productsHolder = [];
        $productsDataSets = explode(',', $this->products);
        foreach ($productsDataSets as $key => $productDataSet) {
            list($fixtureClass, $dataSet) = explode('::', $productDataSet);
            /** @var FixtureInterface[] $productsHolder */
            $productsHolder[$key] = $this->fixtureFactory->createByCode(
                trim($fixtureClass),
                ['dataSet' => trim($dataSet)]
            );
            $productsHolder[$key]->persist();
        }
        return ['productsHolder' => $productsHolder];
    }
}
