<?php
/**
 * Contains logic for finding class filepaths.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Code\Generator;


use Magento\Framework\App\Filesystem\DirectoryList;

class FileResolver
{
    /**
     * Find a file in include path. Include path is set in composer.json or with set_include_path()
     *
     * @param string $class
     * @return string|bool
     */
    public function getFile($class)
    {
        $relativePath = $this->getFilePath($class);
        return stream_resolve_include_path($relativePath);
    }

    /**
     * Get relative file path for specified class
     *
     * @static
     * @param string $class
     * @return string
     */
    public function getFilePath($class)
    {
        return ltrim(str_replace(array('_', '\\'), '/', $class), '/') . '.php';
    }

    /**
     * Add specified path(s) to the current include_path
     *
     * @param string|array $path
     * @param bool $prepend Whether to prepend paths or to append them
     * @return void
     */
    public static function addIncludePath($path, $prepend = true)
    {
        $includePathExtra = implode(PATH_SEPARATOR, (array)$path);
        $includePath = get_include_path();
        $pathSeparator = $includePath && $includePathExtra ? PATH_SEPARATOR : '';
        if ($prepend) {
            $includePath = $includePathExtra . $pathSeparator . $includePath;
        } else {
            $includePath = $includePath . $pathSeparator . $includePathExtra;
        }
        set_include_path($includePath);
    }
} 