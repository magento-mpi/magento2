<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model;

use Magento\Integration\Model\Cache\Type;
/**
 * Integration Config Model.
 *
 * This is a parent class for storing information about Integrations.
 */
class Config
{
    const CACHE_ID = 'integration';

    /**
     * @var \Magento\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Integration\Model\Config\Reader
     */
    protected $_configReader;

    /**
     * Array of integrations
     *
     * @var array
     */
    protected $_integrations;

    /**
     * @param Cache\Type $configCacheType
     * @param Config\Reader $configReader
     */
    public function __construct(
        Cache\Type $configCacheType,
        Config\Reader $configReader
    ) {
        $this->_configCacheType = $configCacheType;
        $this->_configReader = $configReader;
    }

    /**
     * Return integrations loaded from cache if enabled or from files merged previously
     *
     * @return array
     */
    public function getIntegrations()
    {
        if (null === $this->_integrations) {
            $integrations = $this->_configCacheType->load(self::CACHE_ID);
            if ($integrations && is_string($integrations)) {
                $this->_integrations = unserialize($integrations);
            } else {
                $this->_integrations = $this->_configReader->read();
                $this->_configCacheType->save(serialize($this->_integrations), self::CACHE_ID, array(Type::CACHE_TAG));
            }
        }
        return $this->_integrations;
    }
}
