<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Helper\File;

/**
 * Class Media
 */
class Media extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * @var \Magento\Core\Model\Date
     */
    protected $_date;

    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * Constructor
     *
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Date $date
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Date $date
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->_date = $date;
     }

    /**
     * Collect file info
     *
     * Return array(
     *  filename    => string
     *  content     => string|bool
     *  update_time => string
     *  directory   => string
     *
     * @param string $mediaDirectory
     * @param string $path
     * @return array
     * @throws \Magento\Core\Exception
     */
    public function collectFileInfo($mediaDirectory, $path)
    {
        $path = ltrim($path, '\\/');
        $fullPath = $mediaDirectory . '/' . $path;

        $dir = $this->filesystem->getDirectoryRead(\Magento\Filesystem\DirectoryList::MEDIA);
        $relativePath = $dir->getRelativePath($fullPath);
        if (!$dir->isFile($relativePath)) {
            throw new \Magento\Core\Exception(__('File %1 does not exist', $fullPath));
        }
        if (!$dir->isReadable($relativePath)) {
            throw new \Magento\Core\Exception(__('File %1 is not readable', $fullPath));
        }

        $path = str_replace(array('/', '\\'), '/', $path);
        $directory = dirname($path);
        if ($directory == '.') {
            $directory = null;
        }

        return array(
            'filename'      => basename($path),
            'content'       => $dir->readFile($relativePath),
            'update_time'   => $this->_date->date(),
            'directory'     => $directory
        );
    }
}
