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
     * @var \Magento\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * @param string $configDirectoryPath
     * @param string $appMode
     */
    public function __construct(
        \Magento\Filesystem\DirectoryList $directoryList,
        $appMode = \Magento\App\State::MODE_DEFAULT
    ) {
        $this->_directoryList = $directoryList;
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
            new \Magento\App\Arguments\FileResolver\Primary(
                new \Magento\Filesystem(
                    $this->_directoryList,
                    new \Magento\Filesystem\Directory\ReadFactory(),
                    new \Magento\Filesystem\Directory\WriteFactory()
                ),
                new \Magento\Config\FileIteratorFactory()
            ),
            new \Magento\ObjectManager\Config\Mapper\Dom(),
            new \Magento\ObjectManager\Config\SchemaLocator(),
            new \Magento\App\Arguments\ValidationState($this->_appMode)
        );

        return $reader->read('primary');
    }
}
