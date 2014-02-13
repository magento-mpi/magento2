<?php
/**
 * Application config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class FileResolver implements \Magento\Config\FileResolverInterface
{
    /**
     * Module configuration file reader
     *
     * @var \Magento\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * File iterator factory
     *
     * @var \Magento\Config\FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param \Magento\Module\Dir\Reader $moduleReader
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Config\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\Module\Dir\Reader $moduleReader,
        \Magento\App\Filesystem $filesystem,
        \Magento\Config\FileIteratorFactory $iteratorFactory
    ) {
        $this->iteratorFactory = $iteratorFactory;
        $this->filesystem = $filesystem;
        $this->_moduleReader = $moduleReader;
    }

    /**
     * {@inheritdoc}
     */
    public function get($filename, $scope)
    {
        switch ($scope) {
            case 'primary':
                $directory = $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::CONFIG_DIR);
                $iterator = $this->iteratorFactory->create(
                    $directory,
                    $directory->search('{' . $filename . ',*/' . $filename . '}')
                );
                break;
            case 'global':
                $iterator = $this->_moduleReader->getConfigurationFiles($filename);
                break;
            default:
                $iterator = $this->_moduleReader->getConfigurationFiles($scope . '/' . $filename);
                break;
        }
        return $iterator;
    }
}
