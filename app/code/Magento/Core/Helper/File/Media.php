<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Helper\File;

/**
 * Class Media
 */
class Media extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\App\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->_date = $date;
        $this->filesystem = $filesystem;
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
     * @throws \Magento\Framework\Model\Exception
     */
    public function collectFileInfo($mediaDirectory, $path)
    {
        $path = ltrim($path, '\\/');
        $fullPath = $mediaDirectory . '/' . $path;

        $dir = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::MEDIA_DIR);
        $relativePath = $dir->getRelativePath($fullPath);
        if (!$dir->isFile($relativePath)) {
            throw new \Magento\Framework\Model\Exception(__('File %1 does not exist', $fullPath));
        }
        if (!$dir->isReadable($relativePath)) {
            throw new \Magento\Framework\Model\Exception(__('File %1 is not readable', $fullPath));
        }

        $path = str_replace(array('/', '\\'), '/', $path);
        $directory = dirname($path);
        if ($directory == '.') {
            $directory = null;
        }

        return array(
            'filename' => basename($path),
            'content' => $dir->readFile($relativePath),
            'update_time' => $this->_date->date(),
            'directory' => $directory
        );
    }
}
