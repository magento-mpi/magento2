<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Controller_CacheTest extends Mage_Backend_Utility_Controller
{
    /**
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/non_application_cache.php
     */
    public function testFlushAllAction()
    {
        $this->dispatch('backend/admin/cache/flushAll');

        /** @var $cache Mage_Core_Model_Cache */
        $cache = Mage::getModel('Mage_Core_Model_Cache');
        /** @var $cachePool Mage_Core_Model_Cache_Frontend_Pool */
        $this->assertFalse($cache->load('APPLICATION_FIXTURE'));

        $cachePool = Mage::getModel('Mage_Core_Model_Cache_Frontend_Pool');
        /** @var $cacheFrontend Magento_Cache_FrontendInterface */
        foreach ($cachePool as $cacheFrontend) {
            $this->assertFalse($cacheFrontend->getBackend()->load('NON_APPLICATION_FIXTURE'));
        }
    }

    /**
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/non_application_cache.php
     */
    public function testFlushSystemAction()
    {
        $this->dispatch('backend/admin/cache/flushSystem');

        /** @var $cache Mage_Core_Model_Cache */
        $cache = Mage::getModel('Mage_Core_Model_Cache');
        /** @var $cachePool Mage_Core_Model_Cache_Frontend_Pool */
        $this->assertFalse($cache->load('APPLICATION_FIXTURE'));

        $cachePool = Mage::getModel('Mage_Core_Model_Cache_Frontend_Pool');
        /** @var $cacheFrontend Magento_Cache_FrontendInterface */
        foreach ($cachePool as $cacheFrontend) {
            $this->assertSame('non-application cache data',
                $cacheFrontend->getBackend()->load('NON_APPLICATION_FIXTURE'));
        }
    }

    /**
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/all_types_invalidated.php
     * @dataProvider massActionsDataProvider
     * @param array $typesToRefresh
     */
    public function testMassRefreshAction($typesToRefresh = array())
    {
        $this->getRequest()->setParams(array('types' => $typesToRefresh));
        $this->dispatch('backend/admin/cache/massRefresh');

        /** @var $cacheTypeList Mage_Core_Model_Cache_TypeListInterface */
        $cacheTypeList = Mage::getModel('Mage_Core_Model_Cache_TypeListInterface');
        $invalidatedTypes = array_keys($cacheTypeList->getInvalidated());
        $failed = array_intersect($typesToRefresh, $invalidatedTypes);
        $this->assertEmpty($failed, 'Could not refresh following cache types: ' . join(', ', $failed));

    }

    /**
     * @return array
     */
    public function massActionsDataProvider()
    {
        return array(
            'no types' => array(
                array()
            ),
            'existing types' => array(
                array(
                    Mage_Core_Model_Cache_Type_Config::TYPE_IDENTIFIER,
                    Mage_Core_Model_Cache_Type_Layout::TYPE_IDENTIFIER,
                    Mage_Core_Model_Cache_Type_Block::TYPE_IDENTIFIER,
                )
            ),
        );
    }

    /**
     * @dataProvider massActionsInvalidTypesDataProvider
     * @param $action
     */
    public function testMassActionsInvalidTypes($action)
    {
        $this->getRequest()->setParams(array('types' => array('invalid_type_1', 'invalid_type_2', 'config')));
        $this->dispatch('backend/admin/cache/' . $action);
        $this->assertSessionMessages(
            $this->contains("Specified cache type(s) don't exist: invalid_type_1, invalid_type_2"),
            Mage_Core_Model_Message::ERROR
        );
    }

    /**
     * @return array
     */
    public function massActionsInvalidTypesDataProvider()
    {
        return array(
            'enable'  => array('massEnable'),
            'disable' => array('massDisable'),
            'refresh' => array('massRefresh'),
        );
    }
}
