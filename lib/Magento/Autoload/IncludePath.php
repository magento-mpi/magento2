<?php
/**
 * An autoloader that uses include path. Compliant with PSR-0 standard
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
class Magento_Autoload_IncludePath
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
    public static function getFile($class)
    {
        if (strpos($class, self::NS_SEPARATOR) !== false) {
            $class = ltrim(str_replace(self::NS_SEPARATOR, '_', $class), '_');
        }
        $relativePath = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        return stream_resolve_include_path($relativePath);
    }

    /**
     * Append specified path(s) to include_path
     *
     * @param string|array $path
     */
    public static function addIncludePath($path)
    {
        $result = implode(PATH_SEPARATOR, (array)$path);
        $includePath = get_include_path();
        if ($includePath) {
            $result = $result . PATH_SEPARATOR . $includePath;
        }
        set_include_path($result);
    }

    /**
     * Resolve a class file and include it
     *
     * @param $class
     */
    public static function load($class)
    {
        $file = self::getFile($class);
        if ($file) {
            include $file;
        }
    }
}
