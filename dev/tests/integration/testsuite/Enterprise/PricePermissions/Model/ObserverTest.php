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

class Enterprise_PricePermissions_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_Layout */
    protected $_layout = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = new Mage_Core_Model_Layout;
    }

    public function testAdminhtmlBlockHtmlBeforeProductOpt()
    {
        $parentBlock = $this->_layout->createBlock('Mage_Adminhtml_Block_Template', 'admin.product.options');
        $optionsBlock = $this->_layout->addBlock(
            'Mage_Adminhtml_Block_Template',
            'options_box',
            'admin.product.options'
        );

        $this->_initSession();
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

        $this->_initSession();
        $this->_runAdminhtmlBlockHtmlBefore($parentBlock);

        $this->assertFalse($parentBlock->getCanReadPrice());
        $this->assertFalse($selectionBlock->getCanReadPrice());
        $this->assertFalse($parentBlock->getCanEditPrice());
        $this->assertFalse($selectionBlock->getCanEditPrice());
    }

    /**
     * Prepare event and run Enterprise_PricePermissions_Model_Observer::adminhtmlBlockHtmlBefore
     *
     * @param Mage_Core_Block_Abstract $block
     */
    protected function _runAdminhtmlBlockHtmlBefore(Mage_Core_Block_Abstract $block)
    {
        $event = new Varien_Event_Observer();
        $event->setBlock($block);
        $observer = new Enterprise_PricePermissions_Model_Observer;
        $observer->adminControllerPredispatch($event);
        $observer->adminhtmlBlockHtmlBefore($event);
    }

    /**
     * Prepare session
     */
    protected function _initSession()
    {
        $user = new Mage_User_Model_User;
        $user->setId(1)->setRole(true);
        $session = new Mage_Backend_Model_Auth_Session;
        $session->setUpdatedAt(time())->setUser($user);
    }
}
