<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_Config_SectionPool
{
    const CACHE_TAG = 'config_sections';

    /**
     * @var Mage_Core_Model_Config_Section_ReaderPool
     */
    protected $_readerPool;

    /**
     * @var Mage_Core_Model_Config_DataFactory
     */
    protected $_dataFactory;

    /**
     * @var Magento_Cache_FrontendInterface
     */
    protected $_cache;

    /**
     * @var string
     */
    protected $_cacheId;

    /**
     * @var Mage_Core_Model_Config_Data[]
     */
    protected $_sections = array();

    /**
     * @param Mage_Core_Model_Config_Section_ReaderPool $readerList
     * @param Mage_Core_Model_Config_DataFactory $dataFactory
     * @param Magento_Cache_FrontendInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Mage_Core_Model_Config_Section_ReaderPool $readerList,
        Mage_Core_Model_Config_DataFactory $dataFactory,
        Magento_Cache_FrontendInterface $cache,
        $cacheId = 'default_config_cache'
    ) {
        $this->_readerPool = $readerList;
        $this->_dataFactory = $dataFactory;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
    }

    /**
     * Retrieve config section
     *
     * @param string $scopeType
     * @param string $scopeCode
     * @return Mage_Core_Model_Config_Data
     */
    public function getSection($scopeType, $scopeCode = null)
    {
        $code = $scopeType . '|' . $scopeCode;
        if (!isset($this->_sections[$code])) {
            $cacheKey = $this->_cacheId . '|' . $code;
            $data = $this->_cache->load($cacheKey);
            if ($data) {
                $data = unserialize($data);
            } else {
                $reader = $this->_readerPool->getReader($scopeType);
                if ($scopeType === 'default') {
                    $data = $reader->read();
                } else {
                    $data = $reader->read($scopeCode);
                }
                $this->_cache->save(serialize($data), $cacheKey, array(self::CACHE_TAG));
            }
            $this->_sections[$code] = $this->_dataFactory->create(array('data' => $data));
        }
        return $this->_sections[$code];
    }

    /**
     * Clear clear cache of all sections
     */
    public function clean()
    {
        $this->_sections = array();
        $this->_cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(self::CACHE_TAG));
    }
} 
