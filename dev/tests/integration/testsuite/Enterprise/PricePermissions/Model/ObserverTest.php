<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_PricePermissions
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_PricePermissions
 */
class Enterprise_PricePermissions_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    protected $_layout = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = new Mage_Core_Model_Layout;
    }

    public function testAdminhtmlBlockHtmlBeforeAPO()
    {
        $parentBlock = $this->_layout->createBlock('Mage_Adminhtml_Block_Template', 'admin.product.options');
        $optionsBlock = $this->_layout->addBlock(
            'Mage_Adminhtml_Block_Template',
            'options_box',
            'admin.product.options'
        );

        $this->_initSession(false);
        $this->_runAdminhtmlBlockHtmlBefore($parentBlock);

        $this->assertFalse($optionsBlock->getCanEditPrice());
        $this->assertFalse($optionsBlock->getCanReadPrice());
    }

    public function testAdminhtmlBlockHtmlBeforeBundleOpt()
    {
        $parentBlock = $this->_layout->createBlock(
            'Mage_Adminhtml_Block_Template',
            'adminhtml.catalog.product.edit.tab.bundle.option'
        );
        $selectionBlock = $this->_layout->addBlock(
            'Mage_Adminhtml_Block_Template',
            'selection_template',
            'adminhtml.catalog.product.edit.tab.bundle.option'
        );

        $this->_initSession(false);
        $this->_runAdminhtmlBlockHtmlBefore($parentBlock);

        $this->assertFalse($parentBlock->getCanReadPrice());
        $this->assertFalse($selectionBlock->getCanReadPrice());
        $this->assertFalse($parentBlock->getCanEditPrice());
        $this->assertFalse($selectionBlock->getCanEditPrice());
    }

    protected function _runAdminhtmlBlockHtmlBefore($block)
    {
        $event = new Varien_Event_Observer();
        $event->setBlock($block);
        $observer = new Enterprise_PricePermissions_Model_Observer;
        $observer->adminControllerPredispatch($event);
        $observer->adminhtmlBlockHtmlBefore($event);
    }

    protected function _initSession($isAllowed)
    {
        $user = new Mage_Admin_Model_User;
        $user->setId(1)->setRole(true);
        $acl = $this->getMock('Mage_Admin_Model_Resource_Acl', array('isAllowed'));
        $acl->expects(self::any())
            ->method('isAllowed')
            ->will($this->returnValue($isAllowed));
        Mage::getSingleton('Mage_Admin_Model_Session')->setUpdatedAt(time())->setAcl($acl)->setUser($user);
    }
}
