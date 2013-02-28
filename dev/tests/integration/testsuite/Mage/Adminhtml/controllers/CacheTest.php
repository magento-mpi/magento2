<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_CacheTest extends Mage_Backend_Utility_Controller
{
    public function testFlushAllAction()
    {
        $time = time();
        $this->dispatch('backend/admin/cache/flushAll');
        $this->assertTrue($this->_isCacheCleanedAfter($time), 'Cache has not been cleaned');
    }

    public function testFlushSystemAction()
    {
        $time = time();
        $this->dispatch('backend/admin/cache/flushSystem');
        $this->assertTrue(
            $this->_isCacheCleanedAfter($time, array(Mage_Core_Model_AppInterface::CACHE_TAG)),
            'Cache has not been cleaned'
        );
    }

    /**
     * Whether cache for specified tags has been cleaned after specified time
     *
     * @param int $time Timestamp
     * @param array $tags
     * @return bool
     */
    protected function _isCacheCleanedAfter($time, array $tags = array())
    {
        if (empty($tags)) {
            $cacheIds = Mage::app()->getCache()->getLowLevelFrontend()->getIds();
        } else {
            $cacheIds = Mage::app()->getCache()->getLowLevelFrontend()->getIdsMatchingTags($tags);
        }
        $invalidIds = array();
        foreach ($cacheIds as $id) {
            $metadata = Mage::app()->getCache()->getLowLevelFrontend()->getMetadatas($id);
            if ($metadata['mtime'] < $time) {
                $invalidIds[] = $id;
            }
        }
        return count($invalidIds) == 0;
    }

    /**
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/disable_cache.php
     * @dataProvider massActionsDataProvider
     * @param array $typesToEnable
     */
    public function testMassEnableAction($typesToEnable = array())
    {
        $types = array_keys(Mage::getModel('Mage_Core_Model_Cache')->getTypes());
        /** @var $cacheTypes Mage_Core_Model_Cache_Types */
        $cacheTypes = Mage::getModel('Mage_Core_Model_Cache_Types');
        foreach ($types as $type) {
            $this->assertFalse($cacheTypes->isEnabled($type), "Type '$type' must be disabled before the test");
        }

        $this->getRequest()->setParams(array('types' => $typesToEnable));
        $this->dispatch('backend/admin/cache/massEnable');

        $types = array_keys(Mage::getModel('Mage_Core_Model_Cache')->getTypes());
        /** @var $cacheTypes Mage_Core_Model_Cache_Types */
        $cacheTypes = Mage::getModel('Mage_Core_Model_Cache_Types');
        foreach ($types as $type) {
            if (in_array($type, $typesToEnable)) {
                $this->assertTrue($cacheTypes->isEnabled($type), "Type '$type' has not been enabled");
            } else {
                $this->assertFalse($cacheTypes->isEnabled($type), "Type '$type' must remain disabled");
            }
        }
    }

    /**
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/enable_cache.php
     * @dataProvider massActionsDataProvider
     * @param array $typesToDisable
     */
    public function testMassDisableAction($typesToDisable = array())
    {
        $types = array_keys(Mage::getModel('Mage_Core_Model_Cache')->getTypes());
        /** @var $cacheTypes Mage_Core_Model_Cache_Types */
        $cacheTypes = Mage::getModel('Mage_Core_Model_Cache_Types');
        foreach ($types as $type) {
            $this->assertTrue($cacheTypes->isEnabled($type), "Type '$type' must be enabled before the test");
        }

        $this->getRequest()->setParams(array('types' => $typesToDisable));
        $this->dispatch('backend/admin/cache/massDisable');

        $types = array_keys(Mage::getModel('Mage_Core_Model_Cache')->getTypes());
        /** @var $cacheTypes Mage_Core_Model_Cache_Types */
        $cacheTypes = Mage::getModel('Mage_Core_Model_Cache_Types');
        foreach ($types as $type) {
            if (in_array($type, $typesToDisable)) {
                $this->assertFalse($cacheTypes->isEnabled($type), "Type '$type' has not been disabled");
            } else {
                $this->assertTrue($cacheTypes->isEnabled($type), "Type '$type' must remain enabled");
            }
        }
    }

    /**
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/invalidate_cache.php
     * @dataProvider massActionsDataProvider
     * @param array $typesToRefresh
     */
    public function testMassRefreshAction($typesToRefresh = array())
    {
        $this->getRequest()->setParams(array('types' => $typesToRefresh));
        $this->dispatch('backend/admin/cache/massRefresh');

        /** @var $cache Mage_Core_Model_Cache */
        $cache = Mage::getModel('Mage_Core_Model_Cache');
        $invalidatedTypes = array_keys($cache->getInvalidatedTypes());
        $failed = array_intersect($typesToRefresh, $invalidatedTypes);
        $this->assertEmpty($failed, 'Could not refresh following cache types: ' . join(', ', $failed));

    }

    /**
     * @return array
     */
    public function massActionsDataProvider()
    {
        return array(
            'no types'           => array(array()),
            'existing types'     => array(array('config', 'layout', 'block_html')),
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
