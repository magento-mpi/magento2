<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Fake theme class to be used for generate view static files
 */
class Generator_ThemeProxy implements Mage_Core_Model_ThemeInterface
{
    /**
     * @var string
     */
    private $_area;

    /**
     * @var string
     */
    private $_parent;

    /**
     * @var array
     */
    private static $_themesList;

    /**
     * @var array
     */
    private $_descendants = array();

    /**
     * Constructor
     *
     * @param $area
     * @param $themePath
     * @param $parent
     */
    public function __construct($area, $themePath, $parent)
    {
        $this->_area = $area;
        $this->_themePath = $themePath;
        $this->_parent = $parent;
        self::$_themesList[$area . $themePath] = $this;
    }

    /**
     * Get parent theme
     *
     * @return Mage_Core_Model_ThemeInterface
     */
    public function getParentTheme()
    {
        if ($this->_parent && isset(self::$_themesList[$this->_area . $this->_parent])) {
            return self::$_themesList[$this->_area . $this->_parent];
        }
        return null;
    }

    /**
     * Get theme path
     *
     * @return string
     */
    public function getThemePath()
    {
        return $this->_themePath;
    }

    /**
     * Get theme area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_area;
    }

    /**
     * Set parent theme
     *
     * @param Mage_Core_Model_ThemeInterface|null $theme
     */
    public function setParentTheme($theme)
    {
        $this->_parent = $theme;
    }

    /**
     * Add descendant theme
     *
     * @param Mage_Core_Model_ThemeInterface|null $theme
     */
    public function addDescendantTheme($theme)
    {
        $this->_descendants[] = $theme;
    }

    /**
     * Get descendant themes
     *
     * @return array
     */
    public function getDescendants()
    {
        return $this->_descendants;
    }
}
