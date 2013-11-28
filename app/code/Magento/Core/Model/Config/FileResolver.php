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
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryRead;

    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var FileIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @param \Magento\Module\Dir\Reader $moduleReader
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Config\FileIteratorFactory $iteratorFactory
     */
    public function __construct(
        \Magento\Module\Dir\Reader $moduleReader,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Config\FileIteratorFactory $iteratorFactory
    ) {
        $this->directoryRead = $filesystem->getDirectoryRead(\Magento\Filesystem::APP);
        $this->iteratorFactory = $iteratorFactory;
        $this->filesystem = $filesystem;
        $this->_moduleReader = $moduleReader;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        switch ($scope) {
            case 'primary':
                $fileList = $this->directoryRead->search('#/' . $filename . '$#');
                break;
            case 'global':
                $fileList = $this->_moduleReader->getConfigurationFiles($filename);
                break;
            default:
                $fileList = $this->_moduleReader->getConfigurationFiles($scope . '/' . $filename);
                break;
        }
        $output = array();
        foreach ($fileList as $file) {
            $output[] = $this->directoryRead->getRelativePath($file);
        }
//        absolute pathes here
        return $this->iteratorFactory->create(array(
            'filesystem'    => $this->filesystem,
            'paths'         => $output
        ));
    }
}
