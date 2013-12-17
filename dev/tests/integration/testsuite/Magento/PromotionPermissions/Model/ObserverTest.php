<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PromotionPermissions
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PromotionPermissions\Model;

/**
 * @magentoAppArea adminhtml
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleListMock;

    protected function setUp()
    {
        $this->markTestSkipped();
        $this->_moduleListMock = $this->getMock('Magento\Module\ModuleListInterface');
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->addSharedInstance($this->_moduleListMock, 'Magento\Module\ModuleList');
        $objectManager->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $this->_layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\LayoutInterface');
    }

    /**
     * @dataProvider blockHtmlBeforeDataProvider
     * @magentoAppIsolation enabled
     */
    public function testAdminhtmlBlockHtmlBefore($parentBlock, $childBlock)
    {
        $block = $this->_layout->createBlock('Magento\Backend\Block\Template', $parentBlock);
        $this->_layout->addBlock('Magento\Backend\Block\Template', $childBlock, $parentBlock);
        $gridBlock = $this->_layout->addBlock(
            'Magento\Backend\Block\Template',
            'banners_grid_serializer',
            $childBlock
        );

        $this->assertSame(
            $gridBlock,
            $this->_layout->getChildBlock($childBlock, 'banners_grid_serializer')
        );
        $this->_moduleListMock->expects($this->any())->method('getModule')->with('Magento_Banner')
            ->will($this->returnValue(true));
        $event = new \Magento\Event\Observer();
        $event->setBlock($block);
        $observer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\PromotionPermissions\Model\Observer');
        $observer->adminhtmlBlockHtmlBefore($event);

        $this->assertFalse($this->_layout->getChildBlock($childBlock, 'banners_grid_serializer'));
    }

    /**
     * @return array
     */
    public function blockHtmlBeforeDataProvider()
    {
        return array(
            array('promo_quote_edit_tabs', 'salesrule.related.banners'),
            array('promo_catalog_edit_tabs', 'catalogrule.related.banners'),
        );
    }
}
