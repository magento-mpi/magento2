<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Extension packages files collection
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Model\Extension;

class Collection extends \Magento\Data\Collection\Filesystem
{
    /**
     * Files and folders regexsp
     *
     * @var string
     */
    protected $_allowedDirsMask     = '/^[a-z0-9\.\-]+$/i';
    protected $_allowedFilesMask    = '/^[a-z0-9\.\-\_]+\.(xml|ser)$/i';
    protected $_disallowedFilesMask = '/^package\.xml$/i';

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Filesystem\Directory\Write
     */
    protected $connectDirectory;

    /**
     * Set base dir
     *
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\App\Filesystem $filesystem
     */
    public function __construct(\Magento\Core\Model\EntityFactory $entityFactory, \Magento\App\Filesystem $filesystem)
    {
        parent::__construct($entityFactory);
        $this->filesystem = $filesystem;
        $this->connectDirectory = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        $this->connectDirectory->create('connect');
        $this->addTargetDir($this->connectDirectory->getAbsolutePath('connect'));
    }

    /**
     * Row generator
     *
     * @param string $filename
     * @return array
     */
    protected function _generateRow($filename)
    {
        $row = parent::_generateRow($filename);
        $row['package'] = preg_replace('/\.(xml|ser)$/', '',
            str_replace($this->connectDirectory->getAbsolutePath('connect/'), '', $filename));
        $row['filename_id'] = $row['package'];
        $folder = explode('/', $row['package']);
        array_pop($folder);
        $row['folder'] = '/';
        if (!empty($folder)) {
            $row['folder'] = implode('/', $folder) . '/';
        }
        return $row;
    }

    /**
     * Get all folders as options array
     *
     * @return array
     */
    public function collectFolders()
    {
        $collectFiles = $this->_collectFiles;
        $collectDirs = $this->_collectDirs;
        $this->setCollectFiles(false)->setCollectDirs(true);

        $this->_collectRecursive($this->connectDirectory->getAbsolutePath('connect'));
        $result = array('/' => '/');
        foreach ($this->_collectedDirs as $dir) {
            $dir = substr($this->connectDirectory->getRelativePath($dir), strlen('connect/')) . '/';
            $result[$dir] = $dir;
        }

        $this->setCollectFiles($collectFiles)->setCollectDirs($collectDirs);
        return $result;
    }

}
