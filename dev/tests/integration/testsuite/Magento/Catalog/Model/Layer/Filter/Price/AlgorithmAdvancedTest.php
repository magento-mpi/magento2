<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Filter\Price;

use Magento\Framework\Object;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Price.
 *
 * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/Price/_files/products_advanced.php
 */
class AlgorithmAdvancedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Engine
     * @covers \Magento\Framework\Search\Dynamic\Algorithm::calculateSeparators
     */
    public function testWithoutLimits()
    {
        $layer = $this->createLayer();
        $priceResource = $this->createPriceResource($layer);
        $interval = $this->createInterval($priceResource);

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\TestFramework\Request');
        $request->setParam('price', null);
        $model = $this->_prepareFilter($layer, $priceResource);
        $this->assertEquals(
            array(
                0 => array('from' => 0, 'to' => 20, 'count' => 3),
                1 => array('from' => 20, 'to' => '', 'count' => 4)
            ),
            $model->calculateSeparators($interval)
        );
    }

    /**
     * Prepare price filter model
     *
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Resource\Layer\Filter\Price $priceResource
     * @param \Magento\TestFramework\Request|null $request
     * @internal param \Magento\CatalogSearch\Model\Price\Interval $interval
     * @return \Magento\Framework\Search\Dynamic\Algorithm
     */
    protected function _prepareFilter($layer, $priceResource, $request = null)
    {
        /** @var \Magento\Framework\Search\Dynamic\Algorithm $model */
        $model = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Search\Dynamic\Algorithm');
        /** @var $filter \Magento\Catalog\Model\Layer\Filter\Price */
        $filter = Bootstrap::getObjectManager()
            ->create(
                'Magento\Catalog\Model\Layer\Filter\Price',
                array('layer' => $layer, 'resource' => $priceResource, 'priceAlgorithm' => $model)
            );
        $filter->setLayer($layer)->setAttributeModel(new Object(array('attribute_code' => 'price')));
        if (!is_null($request)) {
            $filter->apply(
                $request,
                Bootstrap::getObjectManager()->get(
                    'Magento\Framework\View\LayoutInterface'
                )->createBlock(
                    'Magento\Framework\View\Element\Text'
                )
            );
            $interval = $filter->getInterval();
            if ($interval) {
                $model->setLimits($interval[0], $interval[1]);
            }
        }
        $collection = $layer->getProductCollection();
        $model->setStatistics(
            $collection->getMinPrice(),
            $collection->getMaxPrice(),
            $collection->getPriceStandardDeviation(),
            $collection->getPricesCount()
        );
        return $model;
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Engine
     * @covers \Magento\Framework\Search\Dynamic\Algorithm::calculateSeparators
     */
    public function testWithLimits()
    {
        $this->markTestIncomplete('Bug MAGE-6561');

        $layer = $this->createLayer();
        $priceResource = $this->createPriceResource($layer);
        $interval = $this->createInterval($priceResource);

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\TestFramework\Request');
        $request->setParam('price', '10-100');
        $model = $this->_prepareFilter($layer, $priceResource, $request);
        $this->assertEquals(
            array(
                0 => array('from' => 10, 'to' => 20, 'count' => 2),
                1 => array('from' => 20, 'to' => 100, 'count' => 2)
            ),
            $model->calculateSeparators($interval)
        );
    }

    /**
     * @return \Magento\Catalog\Model\Layer
     */
    protected function createLayer()
    {
        $layer = Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer\Category');
        $layer->setCurrentCategory(4);
        $layer->setState(
            Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Layer\State')
        );
        return $layer;
    }

    /**
     * @param $layer
     * @return \Magento\Catalog\Model\Resource\Layer\Filter\Price
     */
    protected function createPriceResource($layer)
    {
        return Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Resource\Layer\Filter\Price', ['layer' => $layer]);
    }

    /**
     * @param $priceResource
     * @return \Magento\CatalogSearch\Model\Price\Interval
     */
    protected function createInterval($priceResource)
    {
        return Bootstrap::getObjectManager()
            ->create('Magento\CatalogSearch\Model\Price\Interval', ['resource' => $priceResource]);
    }
}
