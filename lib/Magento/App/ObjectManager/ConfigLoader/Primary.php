<?php
/**
 * Primary configuration loader for application object manager
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\App\ObjectManager\ConfigLoader;

class Primary
{
    /**
     * Application mode
     *
     * @var string
     */
    protected $_appMode;

    /**
     * @var string
     */
    protected $_configDirectoryPath;

    /**
     * @param string $configDirectoryPath
     * @param string $appMode
     */
    public function __construct(
        $configDirectoryPath,
        $appMode = \Magento\App\State::MODE_DEFAULT
    ) {
        $this->_configDirectoryPath = $configDirectoryPath;
        $this->_appMode = $appMode;
    }

    /**
     * Retrieve merged configuration from primary config files
     *
     * @return array
     */
    public function load()
    {
        $reader = new \Magento\ObjectManager\Config\Reader\Dom(
            new \Magento\App\Config\FileResolver\Primary(
                new \Magento\Filesystem(
                    new \Magento\Filesystem\DirectoryList($this->_configDirectoryPath),
                    new \Magento\Filesystem\Directory\ReadFactory(),
                    new \Magento\Filesystem\Directory\WriteFactory()
                ),
                new \Magento\Config\FileIteratorFactory()
            ),
            new \Magento\ObjectManager\Config\Mapper\Dom(),
            new \Magento\ObjectManager\Config\SchemaLocator(),
            new \Magento\App\Config\ValidationState($this->_appMode)
        );

        return $reader->read('primary');
    }
}
