<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document;

use Magento\Doc\Document\Content\Reader;
use Magento\Framework\App\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Content
 * @package Magento\Doc\Document
 */
class Content implements ContentInterface
{
    /**
     * Content reader
     *
     * @var Reader
     */
    protected $reader;

    /**
     * @param Reader $reader
     * @param Filesystem $filesystem
     */
    public function __construct(Reader $reader, Filesystem $filesystem)
    {
        $this->reader = $reader;
        $this->moduleDir = $filesystem->getDirectoryWrite(DirectoryList::MODULES);
    }

    /**
     * Load and merge content files of the given name from all modules
     *
     * @param string $fileName
     * @param null $scope
     * @return string
     */
    public function get($fileName, $scope = null)
    {
        return $this->reader->read($fileName, $scope);
    }

    /**
     * @param string $content
     * @param string $type
     * @param string $module
     * @param string $name
     * @return boolean
     */
    public function write($content, $type, $module, $name)
    {
        try {
            $content = trim($content, "\n");
            $content = html_entity_decode($content);
            if ($module && $name) {
                $path = str_replace('_', '/', $module) . '/docs/content/' . str_replace('_', '/', $name) . '.' . $type;
                $this->moduleDir->writeFile($path, $content);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
