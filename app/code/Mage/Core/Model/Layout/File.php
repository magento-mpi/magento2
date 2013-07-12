<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout file in the file system with context of its identity
 */
class Mage_Core_Model_Layout_File
{
    /**
     * @var string
     */
    private $_filename;

    /**
     * @var string
     */
    private $_module;

    /**
     * @var Mage_Core_Model_ThemeInterface
     */
    private $_theme;

    /**
     * @param string $filename
     * @param string $module
     * @param Mage_Core_Model_ThemeInterface $theme
     */
    public function __construct($filename, $module, Mage_Core_Model_ThemeInterface $theme = null)
    {
        $this->_filename = $filename;
        $this->_module = $module;
        $this->_theme = $theme;
    }

    /**
     * Retrieve full filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * Retrieve name of a file without a directory path
     *
     * @return string
     */
    public function getName()
    {
        return basename($this->_filename);
    }

    /**
     * Retrieve fully-qualified name of a module a file belongs to
     *
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Retrieve instance of a theme a file belongs to
     *
     * @return Mage_Core_Model_ThemeInterface|null
     */
    public function getTheme()
    {
        return $this->_theme;
    }

    /**
     * Whether file is a base one
     *
     * @return bool
     */
    public function isBase()
    {
        return is_null($this->_theme);
    }
}
