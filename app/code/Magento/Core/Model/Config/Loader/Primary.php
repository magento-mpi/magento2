<?php
/**
 * Primary configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Loader;

class Primary implements \Magento\Core\Model\Config\LoaderInterface
{
    /**
     * \Directory registry
     *
     * @var \Magento\Core\Model\Dir
     */
    protected $_dirs;

    /**
     * Local config loader
     *
     * @var \Magento\Core\Model\Config\Loader\Local
     */
    protected $_localLoader;

    /**
     * @var \Magento\Core\Model\Config\BaseFactory
     */
    protected $_prototypeFactory;

    /**
     * @param \Magento\Core\Model\Config\Loader\Local $localLoader
     * @param $dir
     */
    public function __construct(\Magento\Core\Model\Config\Loader\Local $localLoader, $dir)
    {
        $this->_localLoader = $localLoader;
        $this->_dir = $dir;
    }

    /**
     * Load primary configuration
     *
     * @param \Magento\Core\Model\Config\Base $config
     */
    public function load(\Magento\Core\Model\Config\Base $config)
    {
        $etcDir = $this->_dir;
        if (!$config->getNode()) {
            $config->loadString('<config/>');
        }
        $files = glob($etcDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'config.xml');
        array_unshift($files, $etcDir . DIRECTORY_SEPARATOR . 'config.xml');
        // 1. app/etc/*.xml (except local config)
        foreach ($files as $filename) {
            $baseConfig = new \Magento\Core\Model\Config\Base('<config/>');
            $baseConfig->loadFile($filename);
            $config->extend($baseConfig);
        }
        // 2. local configuration
        $this->_localLoader->load($config);
    }
}
