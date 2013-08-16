<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_PromotionPermissions
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Enterprise_PromotionPermissions_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout = null;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleListMock;

    protected function setUp()
    {
        $this->_moduleListMock = $this->getMock('Magento_Core_Model_ModuleListInterface');
        Mage::getObjectManager()->addSharedInstance($this->_moduleListMock, 'Magento_Core_Model_ModuleList');
        Mage::getObjectManager()->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        $this->_layout = Mage::getModel('Magento_Core_Model_Layout');
    }

    /**
     * @dataProvider blockHtmlBeforeDataProvider
     */
    public function testAdminhtmlBlockHtmlBefore($parentBlock, $childBlock)
    {
        $block = $this->_layout->createBlock('Magento_Adminhtml_Block_Template', $parentBlock);
        $this->_layout->addBlock('Magento_Adminhtml_Block_Template', $childBlock, $parentBlock);
        $gridBlock = $this->_layout->addBlock(
            'Magento_Adminhtml_Block_Template',
            'banners_grid_serializer',
            $childBlock
        );

        $this->assertSame(
            $gridBlock,
            $this->_layout->getChildBlock($childBlock, 'banners_grid_serializer')
        );
        $this->_moduleListMock->expects($this->any())->method('getModule')->with('Enterprise_Banner')
            ->will($this->returnValue(true));
        $event = new Magento_Event_Observer();
        $event->setBlock($block);
        $observer = Mage::getModel('Enterprise_PromotionPermissions_Model_Observer');
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
