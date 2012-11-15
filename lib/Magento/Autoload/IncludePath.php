<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * An autoloader compliant with PSR-0 standard that uses include path
 */
class Magento_Autoload_IncludePath
{
    /**
     * Namespaces separator
     */
    const NS_SEPARATOR = '\\';

    /**
     * Auto load class file
     *
     * @param string $class class name
     */
    public function autoload($class)
    {
        if (strpos($class, self::NS_SEPARATOR) !== false) {
            $class = str_replace(self::NS_SEPARATOR, '_', ltrim($class, self::NS_SEPARATOR));
        }
        $relativePath = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        $absolutePath = stream_resolve_include_path($relativePath);
        if ($absolutePath) {
            include $absolutePath;
        }
    }

    /**
     * Add specified path(s) to include_path
     *
     * @param string|array $path
     * @param bool $append
     * @return Magento_Autoload_IncludePath
     */
    public function addIncludePath($path, $append = false)
    {
        if (!is_array($path)) {
            $path = array($path);
        }
        if ($append) {
            array_unshift($path, get_include_path());
        } else {
            $path[] = get_include_path();
        }
        set_include_path(implode(PATH_SEPARATOR, $path));
        return $this;
    }
}
