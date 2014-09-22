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

/**
 * Interface ContentInterface
 * @package Magento\Doc\Document
 */
interface ContentInterface
{
    /**
     * Load and merge content files of the given name from all modules
     *
     * @param string $fileName
     * @param null $scope
     * @return string
     */
    public function get($fileName, $scope = null);

    /**
     * @param string $content
     * @param string $type
     * @param string $module
     * @param string $name
     * @return string
     */
    public function write($content, $type, $module, $name);
}
