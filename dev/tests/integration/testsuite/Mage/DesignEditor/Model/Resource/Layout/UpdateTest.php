<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Resource_Layout_UpdateTest extends PHPUnit_Framework_TestCase
{
    /*
     * Test theme id
     */
    protected $_themeId;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Design_PackageInterface
     */
    protected $_design;

    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_design = $this->_objectManager->get('Mage_Core_Model_Design_PackageInterface');

        $this->_themeId = $this->_design->getDesignTheme()->getThemeId();
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');
        $theme->load('Test Theme', 'theme_title');
        $this->_design->getDesignTheme()->setThemeId($theme->getId());
    }

    protected function tearDown()
    {
        $this->_design->getDesignTheme()->setThemeId($this->_themeId);
    }

    /**
     * @magentoDataFixture Mage/Core/_files/layout_update.php
     */
    public function testFetchUpdatesByHandle()
    {
        /** @var $resourceLayoutUpdate Mage_DesignEditor_Model_Resource_Layout_Update */
        $resourceLayoutUpdate = $this->_objectManager->create('Mage_DesignEditor_Model_Resource_Layout_Update');
        $result = $resourceLayoutUpdate->fetchUpdatesByHandle('test_handle');
        $this->assertEquals('not_temporarytemporary', $result);
    }

    /**
     * @magentoDataFixture Mage/Core/_files/layout_update.php
     */
    public function testMakeTemporaryLayoutUpdatesPermanent()
    {
        /** @var $coreLayoutUpdate Mage_Core_Model_Resource_Layout_Update */
        $coreLayoutUpdate = $this->_objectManager->create('Mage_Core_Model_Resource_Layout_Update');
        $resultBefore = $coreLayoutUpdate->fetchUpdatesByHandle('test_handle');
        $this->assertEquals('not_temporary', $resultBefore);

        /** @var $vdeLayoutUpdate Mage_DesignEditor_Model_Resource_Layout_Update */
        $vdeLayoutUpdate = $this->_objectManager->create('Mage_DesignEditor_Model_Resource_Layout_Update');
        $vdeLayoutUpdate->makeTemporaryLayoutUpdatesPermanent($this->_design->getDesignTheme()->getThemeId(),
            array(Mage_Core_Model_AppInterface::ADMIN_STORE_ID)
        );

        $resultAfter = $coreLayoutUpdate->fetchUpdatesByHandle('test_handle');
        $this->assertEquals('not_temporarytemporary', $resultAfter);
    }
}
