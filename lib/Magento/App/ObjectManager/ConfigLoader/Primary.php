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
     * @var \Magento\App\Dir
     */
    protected $_dirs;

    /**
     * @param \Magento\App\Dir $dirs
     * @param string $appMode
     */
    public function __construct(\Magento\App\Dir $dirs, $appMode = \Magento\App\State::MODE_DEFAULT)
    {
        $this->_dirs = $dirs;
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
                    new \Magento\Filesystem\DirectoryList($this->_dirs->getDir()),
                    new \Magento\Filesystem\Directory\ReadFactory(),
                    new \Magento\Filesystem\Directory\WriteFactory(),
                    new \Magento\Filesystem\Adapter\Local()
                ),
                new \Magento\App\Config\FileResolver\FileIteratorFactory()
            ),
            new \Magento\ObjectManager\Config\Mapper\Dom(),
            new \Magento\ObjectManager\Config\SchemaLocator(),
            new \Magento\App\Config\ValidationState($this->_appMode)
        );

        return $reader->read('primary');
    }
}
