<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CatalogUrlRewrite\Observer;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class CategoryUrlPathAutogeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Observer\CategoryUrlPathAutogenerator */
    protected $unit;
    /** @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject */
    protected $observer;
    /** @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject */
    protected $category;
    /** @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator|\PHPUnit_Framework_MockObject_MockObject */
    protected $categoryUrlPathGenerator;

    protected function setUp()
    {
        $this->observer = $this->getMock(
            'Magento\Framework\Event\Observer',
            ['getEvent', 'getCategory'],
            [],
            '',
            false
        );
        $this->category = $this->getMock(
            'Magento\Catalog\Model\Category',
            ['getUrlKey', 'setUrlKey', 'setUrlPath'],
            [],
            '',
            false
        );
        $this->observer->expects($this->any())->method('getEvent')->willReturnSelf();
        $this->observer->expects($this->any())->method('getCategory')->willReturn($this->category);
        $this->categoryUrlPathGenerator = $this->getMock(
            'Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator',
            [],
            [],
            '',
            false
        );

        $this->unit = (new ObjectManagerHelper($this))->getObject(
            'Magento\CatalogUrlRewrite\Observer\CategoryUrlPathAutogenerator',
            ['categoryUrlPathGenerator' => $this->categoryUrlPathGenerator]
        );
    }

    public function testInvokeWithGeneration()
    {
        $this->category->expects($this->once())->method('getUrlKey')->willReturn('category');
        $this->category->expects($this->once())->method('setUrlKey')->willReturnSelf();
        $this->category->expects($this->once())->method('setUrlPath')->willReturnSelf();
        $this->unit->invoke($this->observer);
    }

    public function testInvokeWithoutGeneration()
    {
        $this->category->expects($this->once())->method('getUrlKey')->willReturn(false);
        $this->category->expects($this->never())->method('setUrlKey')->willReturnSelf();
        $this->category->expects($this->never())->method('setUrlPath')->willReturnSelf();
        $this->unit->invoke($this->observer);
    }
}
