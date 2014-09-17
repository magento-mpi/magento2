<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Category;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Category\View
     */
    protected $block;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    protected function setUp()
    {

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->scopeConfigMock = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $context = $objectManager->getObject(
            'Magento\Framework\View\Element\Template\Context',
            [
                'scopeConfig' => $this->scopeConfigMock,
            ]
        );
        $this->block = $objectManager->getObject(
            'Magento\Catalog\Block\Category\View',
            [
                'context' => $context,
            ]
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $categoryTag = array('catalog_category_1');
        $currentCatogoryMock = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $currentCatogoryMock->expects($this->once())->method('getIdentities')->will($this->returnValue($categoryTag));
        $this->block->setCurrentCategory($currentCatogoryMock);
        $this->assertEquals($categoryTag, $this->block->getIdentities());
    }

    /**
     * @param $rssConfig
     * @param $rssCategoryConfig
     * @param $expected
     * @dataProvider isRssCatalogEnableDataProvider
     */
    public function testIsRssCatalogEnable($rssConfig, $rssCategoryConfig, $expected)
    {
        $map = [
            ['rss/config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null, $rssConfig],
            ['rss/catalog/category', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null, $rssCategoryConfig],
        ];
        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->will($this->returnValueMap($map));
        $this->assertEquals($expected, $this->block->isRssCatalogEnable());
    }

    public function isRssCatalogEnableDataProvider()
    {
        return [
            [false, true, false],
            [false, false, false],
            [true, false, false],
            [true, true, true],
        ];
    }
}
