<?php
/**
 * An autoloader that uses include path. Compliant with PSR-0 standard
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Framework\Autoload;

class IncludePath
{
    /**
     * Namespaces separator
     */
    const NS_SEPARATOR = '\\';

    /**
     * Find a file in include path
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
        return ltrim(str_replace(array('_',self::NS_SEPARATOR), '/', $class), '_') . '.php';
    }

    /**
     * Add specified path(s) to the current include_path
     *
     * @param string|array $path
     * @param bool $prepend Whether to prepend paths or to append them
     * @return void
     */
    public function addIncludePath($path, $prepend = true)
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

    /**
     * Resolve a class file and include it
     *
     * @param string $class
     * @return void
     */
    public function load($class)
    {
        $file = $this->getFile($class);
        if ($file) {
            include $file;
        }
    }
}
