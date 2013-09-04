<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code;

/**
 *  Context
 */
class Context
{
    /**
     * Locale directory
     */
    const LOCALE_DIRECTORY = 'i18n';

    /**#@+
     * Context info
     */
    const CONTEXT_TYPE_MODULE = 'module';
    const CONTEXT_TYPE_THEME = 'theme';
    const CONTEXT_TYPE_PUB = 'pub';
    const CONTEXT_TYPE_PATH = 'path';
    /**#@-*/

    /**
     * Get context from file path in array(<context type>, <context value>) format
     * - for module: <Namespace>_<module name>
     * - for theme: <area>/<theme name>
     * - for pub: relative path to file
     * - for arbitrary directory: relative path to file
     *
     * @param string $path
     * @return array
     */
    public function getContextByPath($path)
    {
        if (($value = strstr($path, '/app/code/'))) {
            $type = self::CONTEXT_TYPE_MODULE;
            $value = explode('/', $value);
            $value = $value[3] . '_' . $value[4];
        } elseif (($value = strstr($path, '/app/design/'))) {
            $type = self::CONTEXT_TYPE_THEME;
            $value = explode('/', $value);
            $value = $value[3] . '/' . $value[4];
        } elseif (($value = strstr($path, '/pub/lib/'))) {
            $type = self::CONTEXT_TYPE_PUB;
            $value = ltrim($value, '/');
        } else {
            $type = self::CONTEXT_TYPE_PATH;
            $value = $path;
        }
        return array($type, $value);
    }

    /**
     * Get paths by context
     *
     * @param string $type
     * @param array $value
     * @return string
     * @throws \InvalidArgumentException
     */
    public function buildPathToLocaleDirectoryByContext($type, $value)
    {
        switch ($type) {
            case self::CONTEXT_TYPE_MODULE:
                $path = 'app/code/' . str_replace('_', '/', $value);
                break;
            case self::CONTEXT_TYPE_THEME:
                $path = 'app/design/' . $value;
                break;
            case self::CONTEXT_TYPE_PUB:
                $path = 'pub/lib';
                break;
            case self::CONTEXT_TYPE_PATH:
                $path = ltrim(pathinfo($value, PATHINFO_DIRNAME), '/');
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid meta-type given: "%s".', $type));
        }
        return $path . '/' . self::LOCALE_DIRECTORY . '/';
    }
}
