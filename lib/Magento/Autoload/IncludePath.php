<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * A file locator for autoloader that uses include path. Compliant with PSR-0 standard
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
            $result = $includePath . PATH_SEPARATOR . $result;
        }
        set_include_path($result);
    }
}
