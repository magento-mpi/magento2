<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\I18n\Code\Dictionary;

/**
 *  Context detector
 */
class ContextDetector
{
    /**#@+
     * Phrase context information
     */
    const CONTEXT_TYPE_MODULE = 'module';
    const CONTEXT_TYPE_THEME = 'theme';
    const CONTEXT_TYPE_PUB = 'pub';
    /**#@-*/

    /**
     * Get context from file path in array(<context type>, <context value>) format
     * - for module: <Namespace>_<module name>
     * - for theme: <area>/<theme name>
     * - for lib: relative path to file
     *
     * @param string $filePath
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getContext($filePath)
    {
        if (($contextValue = strstr($filePath, '/app/code/'))) {
            $contextType = self::CONTEXT_TYPE_MODULE;
            $contextValue = explode('/', $contextValue);
            $contextValue = $contextValue[3] . '_' . $contextValue[4];
        } elseif (($contextValue = strstr($filePath, '/app/design/'))) {
            $contextType = self::CONTEXT_TYPE_THEME;
            $contextValue = explode('/', $contextValue);
            $contextValue = $contextValue[3] . '/' . $contextValue[4];
        } elseif (($contextValue = strstr($filePath, '/pub/lib/'))) {
            $contextType = self::CONTEXT_TYPE_PUB;
            $contextValue = ltrim($contextValue, '/');
        } else {
            throw new \InvalidArgumentException('Invalid path given: ' . $filePath);
        }
        return array($contextType, $contextValue);
    }
}
