<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Instant resolver, which resolves file paths just by concatenation of their parameters
 */
class Mage_Core_Model_File_Resolver_Fallback_ByParamsOnly
    implements Mage_Core_Model_File_ResolverInterface
{
    /**
     * Base path, where all view files are assumed to be located
     *
     * @var string
     */
    protected $_baseViewPath;

    /**
     * Constructor.
     * Following entries in $params are required: 'area', 'themeModel'.
     * Note: 'locale' is not supported as for now, so it is just ignored!
     *
     * @param Mage_Core_Model_Dir $dirs
     * @param array $params
     * @throws InvalidArgumentException
     */
    public function __construct(Mage_Core_Model_Dir $dirs, $params) {
        if (!array_key_exists('area', $params) || !array_key_exists('themeModel', $params)
        ) {
            throw new InvalidArgumentException("Missing one of the param keys: 'area', 'themeModel'.");
        }
        $this->_baseViewPath = $dirs->getDir(Mage_Core_Model_Dir::PUB_LIB) . DIRECTORY_SEPARATOR
            . $params['area'] . DIRECTORY_SEPARATOR
            . $params['themeModel']->getThemePath() . DIRECTORY_SEPARATOR;
    }

    /**
     * Get a usual file path
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getFile($file, $module = null)
    {
        throw new Mage_Core_Exception('The ByParamsOnly resolver is not designed to handle usual files');
    }

    /**
     * Get locale file path
     *
     * @param string $file
     * @return string
     */
    public function getLocaleFile($file)
    {
        throw new Mage_Core_Exception('The ByParamsOnly resolver is not designed to handle locale files');
    }

    /**
     * Get view file path
     *
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($file, $module = null)
    {
        return $this->_baseViewPath . ($module ? DIRECTORY_SEPARATOR . $module : '')
            . DIRECTORY_SEPARATOR . $file;
    }
}
