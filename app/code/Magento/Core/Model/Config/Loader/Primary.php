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
     * Config Directory
     *
     * @var string
     */
    protected $_dir;

    /**
     * Config factory
     *
     * @var \Magento\Core\Model\Config\BaseFactory
     */
    protected $_prototypeFactory;

    /**
     * @param string $dir
     */
    public function __construct($dir)
    {
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
    }
}
