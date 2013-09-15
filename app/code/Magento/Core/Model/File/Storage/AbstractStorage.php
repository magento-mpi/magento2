<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract file storage model class
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\File\Storage;

abstract class AbstractStorage extends \Magento\Core\Model\AbstractModel
{
    /**
     * Store media base directory path
     *
     * @var string
     */
    protected $_mediaBaseDirectory = null;

    /**
     * Core file storage database
     *
     * @var Magento_Core_Helper_File_Storage_Database
     */
    protected $_coreFileStorageDb = null;

    /**
     * @param Magento_Core_Helper_File_Storage_Database $coreFileStorageDb
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_File_Storage_Database $coreFileStorageDb,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreFileStorageDb = $coreFileStorageDb;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve media base directory path
     *
     * @return string
     */
    public function getMediaBaseDirectory()
    {
        if (null === $this->_mediaBaseDirectory) {
            /** @var $helper \Magento\Core\Helper\File\Storage\Database */
            $helper = $this->_coreFileStorageDb;
            $this->_mediaBaseDirectory = $helper->getMediaBaseDir();
        }

        return $this->_mediaBaseDirectory;
    }

    /**
     * Collect file info
     *
     * Return array(
     *  filename    => string
     *  content     => string|bool
     *  update_time => string
     *  directory   => string
     * )
     *
     * @param  string $path
     * @return array
     */
    public function collectFileInfo($path)
    {
        $path = ltrim($path, '\\/');
        $fullPath = $this->getMediaBaseDirectory() . DS . $path;

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            \Mage::throwException(__('File %1 does not exist', $fullPath));
        }
        if (!is_readable($fullPath)) {
            \Mage::throwException(__('File %1 is not readable', $fullPath));
        }

        $path = str_replace(array('/', '\\'), '/', $path);
        $directory = dirname($path);
        if ($directory == '.') {
            $directory = null;
        }

        return array(
            'filename'      => basename($path),
            'content'       => @file_get_contents($fullPath),
            'update_time'   => \Mage::getSingleton('Magento\Core\Model\Date')->date(),
            'directory'     => $directory
        );
    }
}
