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

/**
 * Test theme service model
 */
class Mage_Core_Model_Theme_ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_Core_Model_Theme_Service::getPhysicalThemes
     */
    public function testGetPhysicalThemesPerPage()
    {
        /** @var $themeService Mage_Core_Model_Theme_Service */
        $themeService = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Service');
        $collection = $themeService->getPhysicalThemes(1, Mage_Core_Model_Resource_Theme_Collection::DEFAULT_PAGE_SIZE);

        $this->assertLessThanOrEqual(
            Mage_Core_Model_Resource_Theme_Collection::DEFAULT_PAGE_SIZE, $collection->count()
        );

        /** @var $theme Mage_Core_Model_Theme */
        foreach ($collection as $theme) {
            $this->assertEquals(Mage_Core_Model_App_Area::AREA_FRONTEND, $theme->getArea());
            $this->assertEquals(Mage_Core_Model_Theme::TYPE_PHYSICAL, $theme->getType());
        }
    }

    /**
     * @covers Mage_Core_Model_Theme_Service::getPhysicalThemes
     */
    public function testGetPhysicalThemes()
    {
        /** @var $themeService Mage_Core_Model_Theme_Service */
        $themeService = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Service');
        $collection = $themeService->getPhysicalThemes();

        $this->assertGreaterThan(0, $collection->count());

        /** @var $theme Mage_Core_Model_Theme */
        foreach ($collection as $theme) {
            $this->assertEquals(Mage_Core_Model_App_Area::AREA_FRONTEND, $theme->getArea());
            $this->assertEquals(Mage_Core_Model_Theme::TYPE_PHYSICAL, $theme->getType());
        }
    }


    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @covers Mage_Core_Model_Theme_Service::reassignThemeToStores
     */
    public function testReassignThemeToStores()
    {
        $originalCount = $this->_getThemeCollection()->count();

        /** @var $themeService Mage_Core_Model_Theme_Service */
        $themeService = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Service');
        /** @var $physicalTheme Mage_Core_Model_Theme_Service */
        $physicalTheme = $themeService->getPhysicalThemes(1, 1)->fetchItem();
        $this->assertTrue((bool)$physicalTheme->getId(), 'Physical theme is not loaded');

        $storeView = Mage::app()->getAnyStoreView()->getId();
        $themeService->reassignThemeToStores($physicalTheme->getId(), array($storeView));
        $this->assertEquals($originalCount + 1, $this->_getThemeCollection()->count());

        $configItem = Mage::getSingleton('Mage_Core_Model_Config_Data')->getCollection()
            ->addFieldToSelect(array('value'))
            ->addFieldToFilter('scope', Mage_Core_Model_Config::SCOPE_STORES)
            ->addFieldToFilter('scope_id', $storeView)
            ->fetchItem();
        $themeId = $this->_getThemeCollection()->setOrder('theme_id', Varien_Data_Collection_Db::SORT_ORDER_ASC)
            ->getLastItem()->getId();

        $this->assertEquals($configItem->getValue(), $themeId);
    }

    /**
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/all_types_enabled.php
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Mage/Core/_files/config_cache.php
     * @magentoDataFixture Mage/Core/_files/layout_cache.php
     * @magentoAppIsolation enabled
     */
    public function testReassignThemeToStoresClearCache()
    {
        /** @var $appCache Mage_Core_Model_Cache */
        $appCache = Mage::getSingleton('Mage_Core_Model_Cache');
        /** @var Mage_Core_Model_Cache_Type_Config $layoutCache */
        $configCache = Mage::getSingleton('Mage_Core_Model_Cache_Type_Config');
        /** @var Mage_Core_Model_Cache_Type_Layout $layoutCache */
        $layoutCache = Mage::getSingleton('Mage_Core_Model_Cache_Type_Layout');

        $this->assertNotEmpty($appCache->load('APPLICATION_FIXTURE'));
        $this->assertNotEmpty($configCache->load('CONFIG_CACHE_FIXTURE'));
        $this->assertNotEmpty($layoutCache->load('LAYOUT_CACHE_FIXTURE'));

        $this->testReassignThemeToStores();

        $this->assertNotEmpty($appCache->load('APPLICATION_FIXTURE'), 'Unrelated cache must be kept');
        $this->assertFalse($layoutCache->load('CONFIG_CACHE_FIXTURE'), 'Config cache must be erased');
        $this->assertFalse($layoutCache->load('LAYOUT_CACHE_FIXTURE'), 'Layout cache must be erased');
    }

    /**
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    protected function _getThemeCollection()
    {
        return Mage::getObjectManager()->create('Mage_Core_Model_Resource_Theme_Collection');
    }
}
