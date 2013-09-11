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
     * Retrieve media base directory path
     *
     * @return string
     */
    public function getMediaBaseDirectory()
    {
        if (null === $this->_mediaBaseDirectory) {
            /** @var $helper \Magento\Core\Helper\File\Storage\Database */
            $helper = \Mage::helper('Magento\Core\Helper\File\Storage\Database');
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
