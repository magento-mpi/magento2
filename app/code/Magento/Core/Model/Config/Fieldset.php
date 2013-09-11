<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Config;

class Fieldset extends \Magento\Core\Model\Config\Base
{
    /**
     * Constructor.
     * Load configuration from enabled modules with appropriate caching.
     *
     * @param \Magento\Core\Model\Config\Modules\Reader $configReader
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Simplexml\Element|string|null $data
     */
    public function __construct(
        \Magento\Core\Model\Config\Modules\Reader $configReader,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        $data = null
    ) {
        parent::__construct($data);
        $cacheId = 'fieldset_config';
        $cachedXml = $configCacheType->load($cacheId);
        if ($cachedXml) {
            $this->loadString($cachedXml);
        } else {
            $config = $configReader->loadModulesConfiguration('fieldset.xml');
            $xmlConfig = $config->getNode();
            $configCacheType->save($xmlConfig->asXML(), $cacheId);
            $this->setXml($xmlConfig);
        }
    }
}
