<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Generator;

class Io
{
    /**
     * Default code generation directory
     * Should correspond the value from \Magento\Filesystem
     */
    const DEFAULT_DIRECTORY = 'var/generation';

    /**
     * \Directory permission for created directories
     */
    const DIRECTORY_PERMISSION = 0777;

    /**
     * Path to directory where new file must be created
     *
     * @var string
     */
    private $_generationDirectory;

    /**
     * Autoloader instance
     *
     * @var \Magento\Autoload\IncludePath
     */
    private $_autoloader;

    /**
     * @var \Magento\Filesystem\Driver\File
     */
    private $filesystemDriver;
    /**
     * @param \Magento\Filesystem\Driver\File   $filesystemDriver
     * @param \Magento\Autoload\IncludePath     $autoLoader
     * @param null $generationDirectory
     */
    public function __construct(
        \Magento\Filesystem\Driver\File $filesystemDriver,
        \Magento\Autoload\IncludePath   $autoLoader = null,
        $generationDirectory = null
    ) {
        $this->_autoloader          = $autoLoader ? : new \Magento\Autoload\IncludePath();
        $this->filesystemDriver     = $filesystemDriver;
        $this->initGeneratorDirectory($generationDirectory);
    }

    /**
     * Get path to generation directory
     *
     * @param $directory
     * @return string
     */
    protected function initGeneratorDirectory($directory = null)
    {
        if ($directory) {
            $this->_generationDirectory = rtrim($directory, '/') . '/';
        } else {
            $this->_generationDirectory = realpath(__DIR__ . '/../../../../') . '/' . self::DEFAULT_DIRECTORY . '/';
        }
    }

    /**
     * @param string $className
     * @return string
     */
    public function getResultFileDirectory($className)
    {
        $fileName = $this->getResultFileName($className);
        $pathParts = explode('/', $fileName);
        unset($pathParts[count($pathParts) - 1]);

        return implode('/', $pathParts) . '/';
    }

    /**
     * @param string $className
     * @return string
     */
    public function getResultFileName($className)
    {
        $autoloader = $this->_autoloader;
        $resultFileName = $autoloader::getFilePath($className);
        return $this->_generationDirectory . $resultFileName;
    }

    /**
     * @param string $fileName
     * @param string $content
     * @return bool
     */
    public function writeResultFile($fileName, $content)
    {
        $content = "<?php\n" . $content;
        return $this->filesystemDriver->filePutContents($fileName, $content);
    }

    /**
     * @return bool
     */
    public function makeGenerationDirectory()
    {
        return $this->_makeDirectory($this->_generationDirectory);
    }

    /**
     * @param string $className
     * @return bool
     */
    public function makeResultFileDirectory($className)
    {
        return $this->_makeDirectory($this->getResultFileDirectory($className));
    }

    /**
     * @return string
     */
    public function getGenerationDirectory()
    {
        return $this->_generationDirectory;
    }

    /**
     * @param string $fileName
     * @return bool
     */
    public function fileExists($fileName)
    {
        return $this->filesystemDriver->isExists($fileName);
    }

    /**
     * @param string $directory
     * @return bool
     */
    private function _makeDirectory($directory)
    {
        if ($this->filesystemDriver->isWritable($directory)) {
            return true;
        }
        try {
            $this->filesystemDriver->createDirectory($directory, self::DIRECTORY_PERMISSION);
            return true;
        } catch (\Magento\Filesystem\FilesystemException $e) {
            return false;
        }
    }
}
