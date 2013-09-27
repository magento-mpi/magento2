<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PricePermissions
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_PricePermissions_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Core_Model_Layout */
    protected $_layout = null;

    protected function setUp()
    {
        parent::setUp();
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        $this->_layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
    }

    public function testAdminhtmlBlockHtmlBeforeProductOpt()
    {
        $parentBlock = $this->_layout->createBlock('Magento_Adminhtml_Block_Template', 'admin.product.options');
        $optionsBlock = $this->_layout->addBlock(
            'Magento_Adminhtml_Block_Template',
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
            'Magento_Adminhtml_Block_Template',
            'adminhtml.catalog.product.edit.tab.bundle.option'
        );
        $selectionBlock = $this->_layout->addBlock(
            'Magento_Adminhtml_Block_Template',
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
     * Prepare event and run Magento_PricePermissions_Model_Observer::adminhtmlBlockHtmlBefore
     *
     * @param Magento_Core_Block_Abstract $block
     */
    protected function _runAdminhtmlBlockHtmlBefore(Magento_Core_Block_Abstract $block)
    {
        $event = new Magento_Event_Observer();
        $event->setBlock($block);
        $observer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_PricePermissions_Model_Observer');
        $observer->adminControllerPredispatch($event);
        $observer->adminhtmlBlockHtmlBefore($event);
    }

    /**
     * Prepare session
     */
    protected function _initSession()
    {
        $user = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_User_Model_User');
        $user->setId(2)->setRole(true);
        $session = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Backend_Model_Auth_Session');
        $session->setUpdatedAt(time())->setUser($user);
    }
}
