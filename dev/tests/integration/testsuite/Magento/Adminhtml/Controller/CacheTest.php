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
class Magento_Adminhtml_Controller_CacheTest extends Magento_Backend_Utility_Controller
{
    /**
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/non_application_cache.php
     */
    public function testFlushAllAction()
    {
        $this->dispatch('backend/admin/cache/flushAll');

        /** @var $cache Magento_Core_Model_Cache */
        $cache = Mage::getModel('Magento_Core_Model_Cache');
        /** @var $cachePool Magento_Core_Model_Cache_Frontend_Pool */
        $this->assertFalse($cache->load('APPLICATION_FIXTURE'));

        $cachePool = Mage::getModel('Magento_Core_Model_Cache_Frontend_Pool');
        /** @var $cacheFrontend \Magento\Cache\FrontendInterface */
        foreach ($cachePool as $cacheFrontend) {
            $this->assertFalse($cacheFrontend->getBackend()->load('NON_APPLICATION_FIXTURE'));
        }
    }

    /**
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/application_cache.php
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/non_application_cache.php
     */
    public function testFlushSystemAction()
    {
        $this->dispatch('backend/admin/cache/flushSystem');

        /** @var $cache Magento_Core_Model_Cache */
        $cache = Mage::getModel('Magento_Core_Model_Cache');
        /** @var $cachePool Magento_Core_Model_Cache_Frontend_Pool */
        $this->assertFalse($cache->load('APPLICATION_FIXTURE'));

        $cachePool = Mage::getModel('Magento_Core_Model_Cache_Frontend_Pool');
        /** @var $cacheFrontend \Magento\Cache\FrontendInterface */
        foreach ($cachePool as $cacheFrontend) {
            $this->assertSame('non-application cache data',
                $cacheFrontend->getBackend()->load('NON_APPLICATION_FIXTURE'));
        }
    }

    /**
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_disabled.php
     * @dataProvider massActionsDataProvider
     * @param array $typesToEnable
     */
    public function testMassEnableAction($typesToEnable = array())
    {
        $this->getRequest()->setParams(array('types' => $typesToEnable));
        $this->dispatch('backend/admin/cache/massEnable');

        /** @var  Magento_Core_Model_Cache_TypeListInterface$cacheTypeList */
        $cacheTypeList = Mage::getModel('Magento_Core_Model_Cache_TypeListInterface');
        $types = array_keys($cacheTypeList->getTypes());
        /** @var $cacheState Magento_Core_Model_Cache_StateInterface */
        $cacheState = Mage::getModel('Magento_Core_Model_Cache_StateInterface');
        foreach ($types as $type) {
            if (in_array($type, $typesToEnable)) {
                $this->assertTrue($cacheState->isEnabled($type), "Type '$type' has not been enabled");
            } else {
                $this->assertFalse($cacheState->isEnabled($type), "Type '$type' must remain disabled");
            }
        }
    }

    /**
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_enabled.php
     * @dataProvider massActionsDataProvider
     * @param array $typesToDisable
     */
    public function testMassDisableAction($typesToDisable = array())
    {
        $this->getRequest()->setParams(array('types' => $typesToDisable));
        $this->dispatch('backend/admin/cache/massDisable');

        /** @var  Magento_Core_Model_Cache_TypeListInterface$cacheTypeList */
        $cacheTypeList = Mage::getModel('Magento_Core_Model_Cache_TypeListInterface');
        $types = array_keys($cacheTypeList->getTypes());
        /** @var $cacheState Magento_Core_Model_Cache_StateInterface */
        $cacheState = Mage::getModel('Magento_Core_Model_Cache_StateInterface');
        foreach ($types as $type) {
            if (in_array($type, $typesToDisable)) {
                $this->assertFalse($cacheState->isEnabled($type), "Type '$type' has not been disabled");
            } else {
                $this->assertTrue($cacheState->isEnabled($type), "Type '$type' must remain enabled");
            }
        }
    }

    /**
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_invalidated.php
     * @dataProvider massActionsDataProvider
     * @param array $typesToRefresh
     */
    public function testMassRefreshAction($typesToRefresh = array())
    {
        $this->getRequest()->setParams(array('types' => $typesToRefresh));
        $this->dispatch('backend/admin/cache/massRefresh');

        /** @var $cacheTypeList Magento_Core_Model_Cache_TypeListInterface */
        $cacheTypeList = Mage::getModel('Magento_Core_Model_Cache_TypeListInterface');
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
                    Magento_Core_Model_Cache_Type_Config::TYPE_IDENTIFIER,
                    Magento_Core_Model_Cache_Type_Layout::TYPE_IDENTIFIER,
                    Magento_Core_Model_Cache_Type_Block::TYPE_IDENTIFIER,
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
            Magento_Core_Model_Message::ERROR
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
