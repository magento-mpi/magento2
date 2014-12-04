<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Catalog\Setup;

/**
 * Class ProductTest
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRun()
    {
        /** @var \Magento\Tools\SampleData\Module\Catalog\Setup\Attribute $attributes */
        $attributes = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\Catalog\Setup\Attribute'
        );
        /** @var \Magento\Tools\SampleData\Module\Catalog\Setup\Category $categories */
        $categories = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\Catalog\Setup\Category'
        );

        $fixtureHelper = $this->getMockBuilder('Magento\Tools\SampleData\Helper\Fixture')
            ->disableOriginalConstructor()->setMethods(['getPath'])
            ->getMock();

        $fixtures = [realpath(__DIR__ . '/../../../_files/catalog_product.csv')];
        foreach ($fixtures as $index => $fixture) {
            $fixtureHelper->expects($this->at($index))->method('getPath')->with($fixture)
                ->will($this->returnValue($fixture));
        }

        /** @var \Magento\Tools\SampleData\Module\Catalog\Setup\Product $products */
        $products = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\Catalog\Setup\Product',
            [
                'fixtureHelper' => $fixtureHelper,
                'fixtures' => $fixtures
            ]
        );

        ob_start();
        $attributes->run();
        $result = ob_get_clean();
        $this->assertContains('Installing catalog attributes', $result);
        $this->assertContains('................................', $result);

        ob_start();
        $categories->run();
        $result = ob_get_clean();
        $this->assertContains('Installing categories', $result);
        $this->assertContains('......................................', $result);

        ob_start();
        $products->run();
        $result = ob_get_clean();
        $this->assertContains('Installing simple products', $result);
        $this->assertContains('.', $result);
    }
}
