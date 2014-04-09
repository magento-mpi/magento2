<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\File;

/**
 * An advanced context that contains information necessary for view files fallback system
 */
class FallbackContext extends Context
{
    /**
     * @var string
     */
    private $area;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param string $baseUrl
     * @param string $areaType
     * @param string $themePath
     * @param string $localeCode
     */
    public function __construct($baseUrl, $areaType, $themePath, $localeCode)
    {
        $this->area = $areaType;
        $this->theme = $themePath;
        $this->locale = $localeCode;
        parent::__construct($baseUrl, \Magento\App\Filesystem::STATIC_VIEW_DIR, $this->generatePath());
    }

    /**
     * Get area code
     *
     * @return string
     */
    public function getAreaCode()
    {
        return $this->area;
    }

    /**
     * Get theme path
     *
     * @return string
     */
    public function getThemePath()
    {
        return $this->theme;
    }

    /**
     * Get locale code
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->locale;
    }

    /**
     * Generate path based on the context parameters
     *
     * @return string
     */
    private function generatePath()
    {
        return $this->area . ($this->theme ? '/' . $this->theme : '') . ($this->locale ? '/' . $this->locale : '');
    }
}
