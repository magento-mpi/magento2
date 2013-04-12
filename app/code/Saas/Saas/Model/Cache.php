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
class Saas_Saas_Model_Cache extends Mage_Core_Model_Cache
{
    /**
     * Refresh cache
     *
     * @param array|string $typeCode
     * @return Mage_Core_Model_CacheInterface
     */
    public function invalidateType($typeCode)
    {
        if (!is_array($typeCode)) {
            $typeCode = array($typeCode);
        }

        $eventManager = $this->_objectManager->get('Mage_Core_Model_Event_Manager');
        $eventManager->dispatch(
            'application_process_refresh_cache',
            array('cache_types' => $typeCode)
        );
        return $this->_callOriginInvalidateType($typeCode);
    }

    /**
     * @param array $typeCode
     * @return Mage_Core_Model_CacheInterface
     */
    protected function  _callOriginInvalidateType($typeCode)
    {
        return parent::invalidateType($typeCode);
    }
}
