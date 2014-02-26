<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\View\Deployment\Version\Storage;

/**
 * Persistence of deployment version of static files in a local file
 */
class File implements \Magento\App\View\Deployment\Version\StorageInterface
{
    /**
     * @var \Magento\Filesystem\Directory\Write
     */
    private $directory;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @param \Magento\App\Filesystem $filesystem
     * @param string $directoryCode
     * @param string $fileName
     */
    public function __construct(
        \Magento\App\Filesystem $filesystem,
        $directoryCode,
        $fileName
    ) {
        $this->directory = $filesystem->getDirectoryWrite($directoryCode);
        $this->fileName = $fileName;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        try {
            return $this->directory->readFile($this->fileName);
        } catch (\Magento\Filesystem\FilesystemException $e) {
            throw new \UnexpectedValueException(
                'Unable to retrieve deployment version of static files from the file system.', 0, $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save($data)
    {
        $this->directory->writeFile($this->fileName, $data, 'w');
    }
}
