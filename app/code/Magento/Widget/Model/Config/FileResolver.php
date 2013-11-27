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
         * @param \Magento\Filesystem $filesystem
         */
        public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Module\Dir\Reader $moduleReader,
        \Magento\Core\Model\Locale\Hierarchy\Config\FileIteratorFactory $iteratorFactory
    ){
        $this->directoryRead    = $filesystem->getDirectoryRead(\Magento\Filesystem::THEMES);
        $this->iteratorFactory  = $iteratorFactory;
        $this->filesystem       = $filesystem;
        $this->_moduleReader    = $moduleReader;
    }

    /**
     * @inheritdoc
     */
    public function get($filename, $scope)
    {
        $fileList = array();
        switch ($scope) {
            case 'global':
                $fileList = $this->_moduleReader->getConfigurationFiles($filename);
                break;
            case 'design':
                $fileList = $this->directoryRead->search('#.*?/' . $filename . '$#');
                break;
            default:
                break;
        }
        return $this->iteratorFactory->create(array(
            'paths' => $fileList,
            'filesystem' => $this->filesystem
        ));
    }
}
