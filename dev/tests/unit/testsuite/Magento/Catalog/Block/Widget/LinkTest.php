<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Widget;

use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\TestFramework\Helper\ObjectManager;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var \Magento\Catalog\Block\Widget\Link
     */
    protected $block;

    protected function setUp()
    {
        $this->storeManager = $this->getMock('Magento\Framework\StoreManagerInterface');
        $this->urlFinder = $this->getMock('Magento\UrlRewrite\Model\UrlFinderInterface');

        $context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $context->expects($this->any())
            ->method('getStoreManager')
            ->will($this->returnValue($this->storeManager));

        $this->block = (new ObjectManager($this))->getObject('Magento\Catalog\Block\Widget\Link', [
            'context' => $context,
            'urlFinder' => $this->urlFinder,
        ]);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Parameter id_path is not set.
     */
    public function testGetHrefWithoutSetIdPath()
    {
        $this->block->getHref();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Wrong id_path structure.
     */
    public function testGetHrefIfSetWrongIdPath()
    {
        $this->block->setData('id_path', 'wrong_id_path');
        $this->block->getHref();
    }

    public function testGetHrefWithSetStoreId()
    {
        $this->block->setData('id_path', 'type/id');
        $this->block->setData('store_id', 'store_id');

        $this->storeManager->expects($this->once())
            ->method('getStore')->with('store_id')
            // interrupt test execution
            ->will($this->throwException(new \Exception()));

        try {
            $this->block->getHref();
        } catch (\Exception $e) {
        }
    }

    public function testGetHrefIfRewriteIsNotFound()
    {
        $this->block->setData('id_path', 'entity_type/entity_id');

        $store = $this->getMock('Magento\Store\Model\Store', ['getId', '__wakeUp'], [], '', false);
        $store->expects($this->any())
            ->method('getId');

        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $this->urlFinder->expects($this->once())->method('findOneByData')
            ->will($this->returnValue(false));

        $this->assertFalse($this->block->getHref());
    }

    /**
     * @param string $url
     * @param string $separator
     * @dataProvider dataProviderForTestGetHrefWithoutUrlStoreSuffix
     */
    public function testGetHrefWithoutUrlStoreSuffix($url, $separator)
    {
        $storeId = 15;
        $storeCode = 'store-code';
        $requestPath = 'request-path';
        $this->block->setData('id_path', 'entity_type/entity_id');

        $rewrite = $this->getMock('Magento\UrlRewrite\Service\V1\Data\UrlRewrite', [], [], '', false);
        $rewrite->expects($this->once())
            ->method('getRequestPath')
            ->will($this->returnValue($requestPath));

        $store = $this->getMock('Magento\Store\Model\Store', ['getId', 'getUrl', 'getCode', '__wakeUp'], [], '', false);
        $store->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($storeId));
        $store->expects($this->once())
            ->method('getUrl')
            ->with('', ['_direct' => $requestPath])
            ->will($this->returnValue($url));
        $store->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue($storeCode));

        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));

        $this->urlFinder->expects($this->once())->method('findOneByData')
            ->with([
                    UrlRewrite::ENTITY_ID => 'entity_id',
                    UrlRewrite::ENTITY_TYPE => 'entity_type',
                    UrlRewrite::STORE_ID => $storeId,
                ])
            ->will($this->returnValue($rewrite));

        $this->assertEquals($url . $separator . '___store=' . $storeCode, $this->block->getHref());
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetHrefWithoutUrlStoreSuffix()
    {
        return [
            ['url', '?'],
            ['url?some_parameter', '&'],
        ];
    }

    public function testGetHrefWithForProductWithCategoryIdParameter()
    {
        $storeId = 15;
        $this->block->setData('id_path', ProductUrlRewriteGenerator::ENTITY_TYPE . '/entity_id/category_id');

        $store = $this->getMock('Magento\Store\Model\Store', ['getId', '__wakeUp'], [], '', false);
        $store->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($storeId));

        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $this->urlFinder->expects($this->once())->method('findOneByData')
            ->with([
                UrlRewrite::ENTITY_ID => 'entity_id',
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::STORE_ID => $storeId,
                UrlRewrite::METADATA => ['category_id' => 'category_id'],
            ])
            ->will($this->returnValue(false));

        $this->block->getHref();
    }
}
