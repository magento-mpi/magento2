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
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    protected $_fileReader;

    /**
     * @param \Magento\Core\Model\Config\Primary $primaryConfig
     * @param \Magento\Core\Model\Config\Modules\Reader $fileReader
     */
    public function __construct(
        \Magento\Core\Model\Config\Primary $primaryConfig,
        \Magento\Core\Model\Config\Modules\Reader $fileReader
    ) {
        $this->_primaryConfig = $primaryConfig;
        $this->_fileReader = $fileReader;
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

        $this->_fileReader->loadModulesConfiguration(array('config.xml'), $config);
        \Magento\Profiler::stop('load_modules_configuration');

        $config->applyExtends();

        \Magento\Profiler::stop('load_modules');
        \Magento\Profiler::stop('config');
    }
}
