<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Catalog\Model\Category;

class CategoryRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Model\CategoryRegistry */
    protected $categoryRegistry;

    /** @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject */
    protected $category;

    protected function setUp()
    {
        $this->category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()->getMock();
        $this->category->expects($this->any())->method('getId')->will($this->returnValue(1));

        $this->categoryRegistry = (new ObjectManager($this))->getObject(
            'Magento\CatalogUrlRewrite\Model\CategoryRegistry',
            ['categories' => [1 => $this->category]]
        );
    }

    public function testGetExistingCategory()
    {
        $this->assertEquals($this->category, $this->categoryRegistry->get(1));
    }

    public function testGetNonExistingCategory()
    {
        $this->assertEquals(null, $this->categoryRegistry->get('no-category'));
    }

    public function testGetList()
    {
        $this->assertEquals([1 => $this->category], $this->categoryRegistry->getList());
    }
}
