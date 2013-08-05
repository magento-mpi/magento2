<?php
/**
 * Saas cache model
 *
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Saas
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Saas_Model_Cache_TypeList extends Mage_Core_Model_Cache_TypeList
{
    /**
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @param Mage_Core_Model_Cache_Config $config
     * @param Mage_Core_Model_Cache_StateInterface $cacheState
     * @param Mage_Core_Model_Cache_InstanceFactory $factory
     * @param Mage_Core_Model_CacheInterface $cache
     * @param Mage_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Mage_Core_Model_Cache_Config $config,
        Mage_Core_Model_Cache_StateInterface $cacheState,
        Mage_Core_Model_Cache_InstanceFactory $factory,
        Mage_Core_Model_CacheInterface $cache,
        Mage_Core_Model_Event_Manager $eventManager
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($config, $cacheState, $factory, $cache);
    }


    /**
     * Refresh cache
     *
     * @param array|string $typeCode
     */
    public function invalidate($typeCode)
    {
        if (!is_array($typeCode)) {
            $typeCode = array($typeCode);
        }
        $this->_eventManager->dispatch(
            'application_process_refresh_cache',
            array('cache_types' => $typeCode)
        );
        $this->_callOriginInvalidateType($typeCode);
    }

    /**
     * Call original invalidated method
     *
     * @param array $typeCode
     */
    protected function  _callOriginInvalidateType($typeCode)
    {
        parent::invalidate($typeCode);
    }
}
