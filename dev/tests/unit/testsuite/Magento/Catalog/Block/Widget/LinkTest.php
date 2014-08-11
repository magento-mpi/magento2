<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Widget;

use Magento\Backend\Model\Config\Backend\Design\Exception;
use Magento\TestFramework\Helper\ObjectManager;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @TODO: UrlRewrite: Build product URL inside particular category
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\UrlRewrite\Service\V1\UrlMatcherInterface
     */
    protected $urlMatcher;

    /**
     * @var \Magento\Catalog\Block\Widget\Link
     */
    protected $block;

    protected function setUp()
    {
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->urlMatcher = $this->getMock('Magento\UrlRewrite\Service\V1\UrlMatcherInterface');

        $context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $context->expects($this->any())
            ->method('getStoreManager')
            ->will($this->returnValue($this->storeManager));

        $this->block = (new ObjectManager($this))->getObject('Magento\Catalog\Block\Widget\Link', [
            'context' => $context,
            'urlMatcher' => $this->urlMatcher,
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

        $this->urlMatcher->expects($this->once())->method('findByEntity')
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
        $this->block->setData('id_path', 'entity_type/entity_id');
        $storeId = 15;
        $storeCode = 'store-code';
        $requestPath = 'request-path';

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

        $this->urlMatcher->expects($this->once())->method('findByEntity')
            ->with('entity_id', 'entity_type', $storeId)
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

    public function testGetHrefWithAdditionalParameters()
    {
        /** @TODO: UrlRewrite: Build product URL inside particular category */
        $this->markTestIncomplete('UrlRewrite: Build product URL inside particular category');
    }
}
