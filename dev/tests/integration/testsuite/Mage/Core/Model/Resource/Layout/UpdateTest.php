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

    protected function setUp()
    {
        $this->_themeId = Mage::getDesign()->getDesignTheme()->getThemeId();
        /** @var $theme Mage_Core_Model_Theme */
        $theme = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $theme->load('Test Theme', 'theme_title');
        Mage::getDesign()->getDesignTheme()->setThemeId($theme->getId());
    }

    protected function tearDown()
    {
        Mage::getDesign()->getDesignTheme()->setThemeId($this->_themeId);
    }

    /**
     * @magentoDataFixture Mage/Core/_files/layout_update.php
     */
    public function testFetchUpdatesByHandle()
    {
        /** @var $resourceLayoutUpdate Mage_Core_Model_Resource_Layout_Update */
        $resourceLayoutUpdate = Mage::getObjectManager()->create('Mage_Core_Model_Resource_Layout_Update');
        $result = $resourceLayoutUpdate->fetchUpdatesByHandle('test_handle');
        $this->assertEquals('not_temporary', $result);
    }
}
