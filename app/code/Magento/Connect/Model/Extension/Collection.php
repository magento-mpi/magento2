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
class Magento_Connect_Model_Extension_Collection extends Magento_Data_Collection_Filesystem
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
     * Base dir where packages are located
     *
     * @var string
     */
    protected $_baseDir = '';

    /**
     * Set base dir
     *
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Model_EntityFactory $entityFactory
     */
    public function __construct(Magento_Core_Model_Dir $dirs, Magento_Core_Model_EntityFactory $entityFactory)
    {
        parent::__construct($entityFactory);
        $this->_baseDir = $dirs->getDir('var') . DS . 'connect';
        $io = new Magento_Io_File();
        $io->setAllowCreateFolders(true)->createDestinationDir($this->_baseDir);
        $this->addTargetDir($this->_baseDir);
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
        $row['package'] = preg_replace('/\.(xml|ser)$/', '', str_replace($this->_baseDir . DS, '', $filename));
        $row['filename_id'] = $row['package'];
        $folder = explode(DS, $row['package']);
        array_pop($folder);
        $row['folder'] = DS;
        if (!empty($folder)) {
            $row['folder'] = implode(DS, $folder) . DS;
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

        $this->_collectRecursive($this->_baseDir);
        $result = array(DS => DS);
        foreach ($this->_collectedDirs as $dir) {
            $dir = str_replace($this->_baseDir . DS, '', $dir) . DS;
            $result[$dir] = $dir;
        }

        $this->setCollectFiles($collectFiles)->setCollectDirs($collectDirs);
        return $result;
    }

}
