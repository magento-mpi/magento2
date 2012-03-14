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
 * @group module:Enterprise_PromotionPermissions
 */
class Enterprise_PromotionPermissions_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    protected $_layout = null;

    protected function setUp()
    {
        $this->_layout = new Mage_Core_Model_Layout;
    }

    /**
     * @dataProvider blockHtmlBeforeDataProvider
     */
    public function testAdminhtmlBlockHtmlBefore($parentBlock, $childBlock)
    {
        $block = $this->_layout->createBlock('Mage_Adminhtml_Block_Template', $parentBlock);
        $this->_layout->addBlock('Mage_Adminhtml_Block_Template', $childBlock, $parentBlock);
        $gridBlock = $this->_layout->addBlock('Mage_Adminhtml_Block_Template', 'banners_grid_serializer', $childBlock);

        $this->_initSession(false);
        $this->assertEquals(
            $gridBlock,
            $this->_layout->getChildBlock($childBlock, 'banners_grid_serializer')
        );
        $this->_runAdminhtmlBlockHtmlBefore($block);

        $this->assertFalse($this->_layout->getChildBlock($childBlock, 'banners_grid_serializer'));
    }

    public function blockHtmlBeforeDataProvider()
    {
        return array(
            array('promo_quote_edit_tabs', 'salesrule.related.banners'),
            array('promo_catalog_edit_tabs', 'catalogrule.related.banners'),
        );
    }

    protected function _runAdminhtmlBlockHtmlBefore($block)
    {
        Mage::getConfig()->setNode('modules/Enterprise_Banner/active', '1');
        $event = new Varien_Event_Observer();
        $event->setBlock($block);
        $observer = new Enterprise_PromotionPermissions_Model_Observer;
        $observer->adminhtmlBlockHtmlBefore($event);
    }

    protected function _initSession($return)
    {
        $user = new Mage_Admin_Model_User;
        $user->setId(1)->setRole(true);
        $acl = $this->getMock('Mage_Admin_Model_Resource_Acl', array('isAllowed'));
        $acl->expects(self::any())
            ->method('isAllowed')
            ->will($this->returnValue($return));
        Mage::getSingleton('Mage_Admin_Model_Session')->setUpdatedAt(time())->setAcl($acl)->setUser($user);
    }
}
