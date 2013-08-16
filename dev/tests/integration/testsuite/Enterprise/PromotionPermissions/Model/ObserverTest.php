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
     * @var Mage_Core_Model_Layout
     */
    protected $_layout = null;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleListMock;

    protected function setUp()
    {
        $this->_moduleListMock = $this->getMock('Mage_Core_Model_ModuleListInterface');
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $objectManager->addSharedInstance($this->_moduleListMock, 'Mage_Core_Model_ModuleList');
        $objectManager->get('Mage_Core_Model_Config_Scope')->setCurrentScope(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        $this->_layout = Mage::getModel('Mage_Core_Model_Layout');
    }

    /**
     * @dataProvider blockHtmlBeforeDataProvider
     */
    public function testAdminhtmlBlockHtmlBefore($parentBlock, $childBlock)
    {
        $block = $this->_layout->createBlock('Mage_Adminhtml_Block_Template', $parentBlock);
        $this->_layout->addBlock('Mage_Adminhtml_Block_Template', $childBlock, $parentBlock);
        $gridBlock = $this->_layout->addBlock('Mage_Adminhtml_Block_Template', 'banners_grid_serializer', $childBlock);

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
