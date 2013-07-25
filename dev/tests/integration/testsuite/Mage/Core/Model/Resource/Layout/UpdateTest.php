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
    /**
     * @var Mage_Core_Model_Resource_Layout_Update
     */
    protected $_resourceModel;

    protected function setUp()
    {
        $this->_resourceModel = Mage::getModel('Mage_Core_Model_Resource_Layout_Update');
    }

    /**
     * @magentoDataFixture Mage/Core/_files/layout_update.php
     */
    public function testFetchUpdatesByHandle()
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = Mage::getModel('Mage_Core_Model_Theme');
        $theme->load('Test Theme', 'theme_title');
        $result = $this->_resourceModel->fetchUpdatesByHandle('test_handle', $theme, Mage::app()->getStore());
        $this->assertEquals('not_temporary', $result);
    }

    /**
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/all_types_enabled.php
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Mage/Core/_files/layout_cache.php
     */
    public function testSaveAfterClearCache()
    {
        /** @var $appCache Mage_Core_Model_Cache */
        $appCache = Mage::getSingleton('Mage_Core_Model_Cache');
        /** @var Mage_Core_Model_Cache_Type_Layout $layoutCache */
        $layoutCache = Mage::getSingleton('Mage_Core_Model_Cache_Type_Layout');

        $this->assertNotEmpty($appCache->load('APPLICATION_FIXTURE'));
        $this->assertNotEmpty($layoutCache->load('LAYOUT_CACHE_FIXTURE'));

        /** @var $layoutUpdate Mage_Core_Model_Layout_Update */
        $layoutUpdate = Mage::getModel('Mage_Core_Model_Layout_Update');
        $this->_resourceModel->save($layoutUpdate);

        $this->assertNotEmpty($appCache->load('APPLICATION_FIXTURE'), 'Non-layout cache must be kept');
        $this->assertFalse($layoutCache->load('LAYOUT_CACHE_FIXTURE'), 'Layout cache must be erased');
    }
}
