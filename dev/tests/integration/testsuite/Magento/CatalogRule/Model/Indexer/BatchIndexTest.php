<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Model\Indexer;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 * @magentoAppArea adminhtml
 */
class BatchIndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\CatalogRule\Model\Resource\Rule
     */
    protected $resourceRule;

    protected function setUp()
    {
        $this->resourceRule = Bootstrap::getObjectManager()->get('Magento\CatalogRule\Model\Resource\Rule');
        $this->product = Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Product');
    }

    /**
     * @magentoDataFixture Magento/CatalogRule/_files/two_rules.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testPriceForSmallBatch()
    {
        $productIds = $this->prepareProducts();

        for ($count=1; $count<14; $count+=2) {
            /**
             * @var IndexBuilder|\PHPUnit_Framework_MockObject_MockObject $indexerBuilder
             */
            $indexerBuilder = $this->getMock(
                'Magento\CatalogRule\Model\Indexer\IndexBuilder',
                ['getBatchCount'],
                [
                    'ruleCollectionFactory' => Bootstrap::getObjectManager()->get(
                        'Magento\CatalogRule\Model\Resource\Rule\CollectionFactory'
                    ),
                    'priceCurrency' => Bootstrap::getObjectManager()->get(
                        'Magento\Framework\Pricing\PriceCurrencyInterface'
                    ),
                    'resource' => Bootstrap::getObjectManager()->get('Magento\Framework\App\Resource'),
                    'storeManager' => Bootstrap::getObjectManager()->get('Magento\Framework\StoreManagerInterface'),
                    'logger' => Bootstrap::getObjectManager()->get('Magento\Framework\Logger'),
                    'eavConfig' => Bootstrap::getObjectManager()->get('Magento\Eav\Model\Config'),
                    'dateFormat' => Bootstrap::getObjectManager()->get('Magento\Framework\Stdlib\DateTime'),
                    'dateTime' => Bootstrap::getObjectManager()->get('Magento\Framework\Stdlib\DateTime\DateTime'),
                    'productFactory' => Bootstrap::getObjectManager()->get('Magento\Catalog\Model\ProductFactory'),
                ]
            );
            $indexerBuilder->expects($this->any())->method('getBatchCount')->will($this->returnValue($count));

            $indexerBuilder->reindexFull();

            foreach ([0, 1] as $customerGroupId) {
                foreach ($productIds as $productId) {
                    $this->assertEquals(7, $this->resourceRule->getRulePrice(true, 1, $customerGroupId, $productId));
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function prepareProducts()
    {
        $this->product->load(1);
        $productSecond = clone $this->product;
        $productSecond->setId(null)
            ->setSku(uniqid($this->product->getSku() . '-'))
            ->setName(uniqid($this->product->getName() . '-'))
            ->setWebsiteIds([1])
            ->save();
        $productThird = clone $this->product;
        $productThird->setId(null)
            ->setSku(uniqid($this->product->getSku() . '-'))
            ->setName(uniqid($this->product->getName() . '-'))
            ->setWebsiteIds([1])
            ->save();
        return [
            $this->product->getId(),
            $productSecond->getId(),
            $productThird->getId(),
        ];
    }
}
