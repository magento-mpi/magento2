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

        /** @var $cache \Magento\Core\Model\Cache */
        $cache = Mage::getModel('Magento\Core\Model\Cache');
        /** @var $cachePool \Magento\Core\Model\Cache\Frontend\Pool */
        $this->assertFalse($cache->load('APPLICATION_FIXTURE'));

        $cachePool = Mage::getModel('Magento\Core\Model\Cache\Frontend\Pool');
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

        /** @var $cache \Magento\Core\Model\Cache */
        $cache = Mage::getModel('Magento\Core\Model\Cache');
        /** @var $cachePool \Magento\Core\Model\Cache\Frontend\Pool */
        $this->assertFalse($cache->load('APPLICATION_FIXTURE'));

        $cachePool = Mage::getModel('Magento\Core\Model\Cache\Frontend\Pool');
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

        /** @var  \Magento\Core\Model\Cache\TypeListInterface$cacheTypeList */
        $cacheTypeList = Mage::getModel('Magento\Core\Model\Cache\TypeListInterface');
        $types = array_keys($cacheTypeList->getTypes());
        /** @var $cacheState \Magento\Core\Model\Cache\StateInterface */
        $cacheState = Mage::getModel('Magento\Core\Model\Cache\StateInterface');
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

        /** @var  \Magento\Core\Model\Cache\TypeListInterface$cacheTypeList */
        $cacheTypeList = Mage::getModel('Magento\Core\Model\Cache\TypeListInterface');
        $types = array_keys($cacheTypeList->getTypes());
        /** @var $cacheState \Magento\Core\Model\Cache\StateInterface */
        $cacheState = Mage::getModel('Magento\Core\Model\Cache\StateInterface');
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

        /** @var $cacheTypeList \Magento\Core\Model\Cache\TypeListInterface */
        $cacheTypeList = Mage::getModel('Magento\Core\Model\Cache\TypeListInterface');
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
                    \Magento\Core\Model\Cache\Type\Config::TYPE_IDENTIFIER,
                    \Magento\Core\Model\Cache\Type\Layout::TYPE_IDENTIFIER,
                    \Magento\Core\Model\Cache\Type\Block::TYPE_IDENTIFIER,
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
            \Magento\Core\Model\Message::ERROR
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
