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
class Mage_Core_Model_Design_FileResolution_Strategy_View_ByParamsOnly
    implements Mage_Core_Model_Design_FileResolution_Strategy_ViewInterface
{
    /**
     * Base path, where all view files are assumed to be located
     *
     * @var string
     */
    protected $_pubViewDir;

    /**
     * @param Mage_Core_Model_Dir $dirs
     */
    public function __construct(Mage_Core_Model_Dir $dirs)
    {
        $this->_pubViewDir = $dirs->getDir(Mage_Core_Model_Dir::STATIC_VIEW);
    }

    /**
     * Get theme file name (e.g. a javascript file).
     * Note: locale is not supported and is just ignored!
     *
     * @param string $area
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $locale
     * @param string $file
     * @param string|null $module
     * @return string
     */
    public function getViewFile($area, Mage_Core_Model_Theme $themeModel, $locale, $file, $module = null)
    {
        return $this->_pubViewDir . DIRECTORY_SEPARATOR . $area . DIRECTORY_SEPARATOR
            . $themeModel->getThemePath() . DIRECTORY_SEPARATOR
            . ($module ? $module . DIRECTORY_SEPARATOR : '')
            . $file;
    }
}
