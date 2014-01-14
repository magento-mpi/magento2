<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\LibraryLoader;

/**
 * Oyejorge library loader
 */
class Oyejorge implements LoaderInterface
{
    /**
     * @var \Magento\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Filesystem\Driver\File
     */
    protected $driverFile;

    public function __construct(
        \Magento\Filesystem\DirectoryList $directoryList,
        \Magento\Filesystem\Driver\File $driverFile
    ) {
        $this->directoryList = $directoryList;
        $this->driverFile = $driverFile;
    }

    /**
     * Include library files
     *
     * @throws \InvalidArgumentException
     */
    public function load()
    {
        if (!class_exists('Less_Parser') && !$this->isExistLibrary()) {
            throw new \InvalidArgumentException('Oyejorge Less library is not present');
        }

        require_once $this->getLibraryPath();
    }

    /**
     * Get is file oyejorge library exists
     *
     * @return bool
     */
    protected function isExistLibrary()
    {
        return $this->driverFile->isExists($this->getLibraryPath());
    }

    /**
     * Get path where should be oyejorge library
     *
     * @return string
     */
    protected function getLibraryPath()
    {
        return $this->directoryList->getDir(\Magento\Filesystem::LIB) . '/oyejorge/phpless/Less.php';
    }
}
