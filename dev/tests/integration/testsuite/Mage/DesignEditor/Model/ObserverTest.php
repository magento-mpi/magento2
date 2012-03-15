<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for skin changing observer
 *
 * @group module:Mage_DesignEditor
 */
class Mage_DesignEditor_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Model_Observer
     */
    protected $_observer;

    /**
     * @var Varien_Event_Observer
     */
    protected $_eventObserver;

    protected function setUp()
    {
        $this->_observer = new Mage_DesignEditor_Model_Observer;

        $this->_eventObserver = new Varien_Event_Observer();
        $this->_eventObserver->setEvent(new Varien_Event(array('layout' => Mage::app()->getLayout())));
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testApplyDesign()
    {
        $newSkin = 'default/default/blank';
        $this->assertNotEquals($newSkin, Mage::getDesign()->getDesignTheme());

        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $session->setSkin($newSkin);

        $this->_observer->applyDesign($this->_eventObserver);
        $this->assertEquals($newSkin, Mage::getDesign()->getDesignTheme());
        $this->assertContains(
            Mage_DesignEditor_Model_Observer::TOOLBAR_HANDLE,
            Mage::app()->getLayout()->getUpdate()->getHandles()
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testApplyCustomSkinChangesNothingWhenNoSkin()
    {
        $currentSkin = Mage::getDesign()->getDesignTheme();
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $this->assertEmpty($session->getSkin());
        $this->_observer->applyDesign($this->_eventObserver);
        $this->assertEquals($currentSkin, Mage::getDesign()->getDesignTheme());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testApplyCustomSkinInactive()
    {
        $newSkin = 'default/default/blank';
        $oldSkin = Mage::getDesign()->getDesignTheme();
        $this->assertNotEquals($newSkin, $oldSkin);

        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $session->setSkin($newSkin);

        $this->_observer->applyDesign($this->_eventObserver);
        $this->assertEquals($oldSkin, Mage::getDesign()->getDesignTheme());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testDisableBlocksOutputCachingInactive()
    {
        Mage::app()->getCacheInstance()->allowUse(Mage_Core_Block_Abstract::CACHE_GROUP);
        $this->_observer->disableBlocksOutputCaching(new Varien_Event_Observer());
        $this->assertTrue(Mage::app()->useCache(Mage_Core_Block_Abstract::CACHE_GROUP));
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testDisableBlocksOutputCachingActive()
    {
        Mage::app()->getCacheInstance()->allowUse(Mage_Core_Block_Abstract::CACHE_GROUP);
        $this->_observer->disableBlocksOutputCaching(new Varien_Event_Observer());
        $this->assertFalse(Mage::app()->useCache(Mage_Core_Block_Abstract::CACHE_GROUP));
    }

    /**
     * @param string $elementName
     * @param string $expectedOutput
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     * @dataProvider wrapPageElementDataProvider
     */
    public function testWrapPageElement($elementName, $expectedOutput)
    {
        // create a layout object mock with fixture data
        $utility = new Mage_Core_Utility_Layout($this);
        $layoutMock = $utility->getLayoutFromFixture(__DIR__ . '/../_files/observer_test.xml');

        // replace layout structure instance (protected argument), so that it could be used in observer
        $structure = new Mage_Core_Model_Layout_Structure;
        $structureProperty = new ReflectionProperty(get_class($layoutMock), '_structure');
        $structureProperty->setAccessible(true);
        $structureProperty->setValue($layoutMock, $structure);

        // load the fixture data. This will populate layout structure as well
        $layoutMock->getUpdate()->addHandle('test_handle')->load();
        $layoutMock->generateXml()->generateBlocks();

        $expectedContent = 'test_content';
        $layoutMock->setRenderingOutput($expectedContent);
        $observer = new Varien_Event_Observer(array(
            'event' => new Varien_Event(array(
                'structure' => $structure,
                'layout' => $layoutMock,
                'element_name' => $elementName,
            ))
        ));

        $this->_observer->wrapPageElement($observer);
        $this->assertEquals(sprintf($expectedOutput, $expectedContent), $layoutMock->getRenderingOutput());
    }

    /**
     * @return array
     */
    public function wrapPageElementDataProvider()
    {
        return array(
            array('test.text', '<br class="vde_marker" block_name="test.text"' . "\n" . '    marker_type="start"/>'
                        . "\n" . '%s<br class="vde_marker" marker_type="end"/>' . "\n"),
            array('toolbar', '%s'),
            array('test.text3', '%s'),
        );
    }
}
