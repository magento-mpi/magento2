<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\ObjectManager\ConfigLoader;

class Primary
{
    /**
     * Application mode
     *
     * @var string
     */
    protected $_appMode;

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_dirs;

    /**
     * @param \Magento\Core\Model\Dir $dirs
     * @param string $appMode
     */
    public function __construct(\Magento\Core\Model\Dir $dirs, $appMode = \Magento\Core\Model\App\State::MODE_DEFAULT)
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
            new \Magento\Core\Model\Config\FileResolver\Primary($this->_dirs),
            new \Magento\ObjectManager\Config\Mapper\Dom(),
            new \Magento\ObjectManager\Config\SchemaLocator(),
            new \Magento\Core\Model\Config\ValidationState($this->_appMode)
        );

        return $reader->read('primary');
    }
}
