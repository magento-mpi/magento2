<?php
/**
 * Application config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Model\Config;

class FileResolver implements \Magento\Framework\Config\FileResolverInterface
{
    /**
     * Module configuration file reader
     *
     * @var \Magento\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $themesDirectory;

    /**
     * @var \Magento\Framework\Config\FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $modulesDirectory;

    /**
     * @param \Magento\Framework\App\Filesystem                   $filesystem
     * @param \Magento\Module\Dir\Reader            $moduleReader
     * @param \Magento\Framework\Config\FileIteratorFactory   $iteratorFactory
     */
    public function __construct(
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Config\FileIteratorFactory $iteratorFactory
    ) {
        $this->themesDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::THEMES_DIR);
        $this->modulesDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::MODULES_DIR);
        $this->iteratorFactory = $iteratorFactory;
        $this->_moduleReader = $moduleReader;
    }

    /**
     * {@inheritdoc}
     */
    public function get($filename, $scope)
    {
        switch ($scope) {
            case 'global':
                $iterator = $this->_moduleReader->getConfigurationFiles($filename);
                break;
            case 'design':
                $iterator = $this->iteratorFactory->create(
                    $this->themesDirectory,
                    $this->themesDirectory->search('/*/*/etc/' . $filename)
                );
                break;
            default:
                $iterator = $this->iteratorFactory->create($this->themesDirectory, array());
                break;
        }
        return $iterator;
    }
}
