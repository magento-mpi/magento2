<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Search;

/**
 * @magentoAppArea adminhtml
 */
class CatalogTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogSearch\Model\Search\Catalog */
    protected $catalogSearch;

    protected function setUp()
    {
        $this->catalogSearch = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('\Magento\Backend\Model\Search\Catalog');
    }

    /**
     * Dataprovider for testLoad
     */
    public function dataProviderForTestLoad()
    {
        return [
            ['StoreTitle', true], // positive case
            ['Some other', false], // negative case
        ];
    }

    /**
     * Check that we can find product by name from previous
     *
     * @dataProvider dataProviderForTestLoad
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple_multistore.php
     */
    public function testLoad($viewName, $expectedResult)
    {
        $results = $this->catalogSearch->setStart(0)
            ->setLimit(20)
            ->setQuery($viewName)
            ->load()
            ->getResults();
        $result = false;
        $product = array_shift($results);
        if ($product
            && $product['type'] == 'Product'
            && $product['name'] == 'Simple Product One'
        ) {
            $result = true;
        }
        $this->assertEquals(
            $expectedResult,
            $result,
            'Can\'t find product "Simple Product One" with name "StoreTitle" on second store view'
        );
    }
}
