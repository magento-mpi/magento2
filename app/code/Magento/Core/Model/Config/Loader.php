<?php
/**
 * Application config loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class Loader implements \Magento\Core\Model\Config\LoaderInterface
{
    /**
     * Primary application configuration
     *
     * @var \Magento\Core\Model\Config\Primary
     */
    protected $_primaryConfig;

    /**
     * @var \Magento\Core\Model\Config\Resource
     */
    protected $_resourceConfig;

    /**
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    protected $_fileReader;

    /**
     * @var \Magento\Core\Model\Config\Loader\Local
     */
    protected $_localLoader;

    /**
     * @param \Magento\Core\Model\Config\Primary $primaryConfig
     * @param \Magento\Core\Model\Config\Resource $resourceConfig
     * @param \Magento\Core\Model\Config\Modules\Reader $fileReader
     * @param \Magento\Core\Model\Config\Loader\Local $localLoader
     */
    public function __construct(
        \Magento\Core\Model\Config\Primary $primaryConfig,
        \Magento\Core\Model\Config\Resource $resourceConfig,
        \Magento\Core\Model\Config\Modules\Reader $fileReader,
        \Magento\Core\Model\Config\Loader\Local $localLoader
    ) {
        $this->_primaryConfig = $primaryConfig;
        $this->_resourceConfig = $resourceConfig;
        $this->_fileReader = $fileReader;
        $this->_localLoader = $localLoader;
    }

    /**
     * Populate configuration object
     *
     * @param \Magento\Core\Model\Config\Base $config
     */
    public function load(\Magento\Core\Model\Config\Base $config)
    {
        if (!$config->getNode()) {
            $config->loadString('<config></config>');
        }

        \Magento\Profiler::start('config');
        \Magento\Profiler::start('load_modules');

        $config->extend($this->_primaryConfig);

        \Magento\Profiler::start('load_modules_configuration');

        $resourceConfig = sprintf('config.%s.xml', $this->_resourceConfig->getResourceConnectionModel('core'));
        $this->_fileReader->loadModulesConfiguration(array('config.xml', $resourceConfig), $config);
        \Magento\Profiler::stop('load_modules_configuration');

        // Prevent local configuration overriding
        $this->_localLoader->load($config);

        $config->applyExtends();

        \Magento\Profiler::stop('load_modules');
        \Magento\Profiler::stop('config');
    }
}
