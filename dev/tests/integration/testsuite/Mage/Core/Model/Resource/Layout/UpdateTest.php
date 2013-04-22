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

class Mage_Core_Model_Resource_Layout_UpdateTest extends PHPUnit_Framework_TestCase
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
    protected $_designPackage;

    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_designPackage = $this->_objectManager->get('Mage_Core_Model_Design_PackageInterface');

        $this->_themeId = $this->_designPackage->getDesignTheme()->getThemeId();
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');
        $theme->load('Test Theme', 'theme_title');
        $this->_designPackage->getDesignTheme()->setThemeId($theme->getId());
    }

    protected function tearDown()
    {
        $this->_designPackage->getDesignTheme()->setThemeId($this->_themeId);
    }

    /**
     * @magentoDataFixture Mage/Core/_files/layout_update.php
     */
    public function testFetchUpdatesByHandle()
    {
        /** @var $resourceLayoutUpdate Mage_Core_Model_Resource_Layout_Update */
        $resourceLayoutUpdate = $this->_objectManager->create('Mage_Core_Model_Resource_Layout_Update');
        $result = $resourceLayoutUpdate->fetchUpdatesByHandle('test_handle');
        $this->assertEquals('not_temporary', $result);
    }
}
