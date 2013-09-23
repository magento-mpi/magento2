<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_Layout_UpdateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Resource_Layout_Update
     */
    protected $_resourceModel;

    protected function setUp()
    {
        $this->_resourceModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Resource_Layout_Update');
    }

    /**
     * @magentoDataFixture Magento/Core/_files/layout_update.php
     */
    public function testFetchUpdatesByHandle()
    {
        /** @var $theme Magento_Core_Model_Theme */
        $theme = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Theme');
        $theme->load('Test Theme', 'theme_title');
        $result = $this->_resourceModel->fetchUpdatesByHandle('test_handle', $theme, Mage::app()->getStore());
        $this->assertEquals('not_temporary', $result);
    }

    /**
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_enabled.php
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Magento/Core/_files/layout_cache.php
     */
    public function testSaveAfterClearCache()
    {
        /** @var $appCache Magento_Core_Model_Cache */
        $appCache = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Cache');
        /** @var Magento_Core_Model_Cache_Type_Layout $layoutCache */
        $layoutCache = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Cache_Type_Layout');

        $this->assertNotEmpty($appCache->load('APPLICATION_FIXTURE'));
        $this->assertNotEmpty($layoutCache->load('LAYOUT_CACHE_FIXTURE'));

        /** @var $layoutUpdate Magento_Core_Model_Layout_Update */
        $layoutUpdate = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Layout_Update');
        $this->_resourceModel->save($layoutUpdate);

        $this->assertNotEmpty($appCache->load('APPLICATION_FIXTURE'), 'Non-layout cache must be kept');
        $this->assertFalse($layoutCache->load('LAYOUT_CACHE_FIXTURE'), 'Layout cache must be erased');
    }
}
