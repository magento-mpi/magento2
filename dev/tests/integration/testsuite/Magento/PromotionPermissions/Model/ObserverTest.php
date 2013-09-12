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

/**
 * @magentoAppArea adminhtml
 */
class Magento_PromotionPermissions_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout = null;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleListMock;

    protected function setUp()
    {
        $this->_moduleListMock = $this->getMock('Magento\Core\Model\ModuleListInterface');
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->addSharedInstance($this->_moduleListMock, 'Magento\Core\Model\ModuleList');
        $objectManager->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $this->_layout = Mage::getModel('Magento\Core\Model\Layout');
    }

    /**
     * @dataProvider blockHtmlBeforeDataProvider
     */
    public function testAdminhtmlBlockHtmlBefore($parentBlock, $childBlock)
    {
        $block = $this->_layout->createBlock('Magento\Adminhtml\Block\Template', $parentBlock);
        $this->_layout->addBlock('Magento\Adminhtml\Block\Template', $childBlock, $parentBlock);
        $gridBlock = $this->_layout->addBlock(
            'Magento\Adminhtml\Block\Template',
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
        $observer = Mage::getModel('Magento\PromotionPermissions\Model\Observer');
        $observer->adminhtmlBlockHtmlBefore($event);

        $this->assertFalse($this->_layout->getChildBlock($childBlock, 'banners_grid_serializer'));
    }

    public function blockHtmlBeforeDataProvider()
    {
        return array(
            array('promo_quote_edit_tabs', 'salesrule.related.banners'),
            array('promo_catalog_edit_tabs', 'catalogrule.related.banners'),
        );
    }
}
